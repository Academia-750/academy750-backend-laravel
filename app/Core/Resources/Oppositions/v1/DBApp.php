<?php
namespace App\Core\Resources\Oppositions\v1;

use App\Core\Resources\Oppositions\v1\Services\ActionForMultipleRecordsService;
use App\Core\Resources\Oppositions\v1\Services\ActionsOppositionsRecords;
use App\Models\Opposition;
use App\Core\Resources\Oppositions\v1\Interfaces\OppositionsInterface;
use App\Models\Subtopic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
//use App\Imports\Api\Oppositions\v1\OppositionsImport;


class DBApp implements OppositionsInterface
{
    protected $model;

    public function __construct(Opposition $opposition ){
        $this->model = $opposition;
    }

    public function index(){
        $queryResults = $this->model::applyFilters()->applySorts()->where('is_available', 'yes')->applyIncludes()->jsonPaginate();

        return $queryResults;
    }

    public function create( $request ): \App\Models\Opposition{
        try {

            DB::beginTransaction();
                $oppositionCreated = $this->model->query()->create([
                    'name' => $request->get('name'),
                    'period' => $request->get('period'),
                    'is_available' => 'yes'
                ]);
            DB::commit();

            return $this->model->applyIncludes()->find($oppositionCreated->id);

        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }

    }

    public function read( $opposition ): \App\Models\Opposition{
        return $this->model->applyIncludes()->find($opposition->getRouteKey());
    }

    public function update( $request, $opposition ): \App\Models\Opposition{
        try {

            DB::beginTransaction();
                $opposition->name = $request->get('name') ?? $opposition->name;
                $opposition->period = $request->get('period') ?? $opposition->period;
                $opposition->save();
            DB::commit();

            return $this->model->applyIncludes()->find($opposition->getRouteKey());

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function delete( $opposition ): void{
        try {

            DB::beginTransaction();
                ActionsOppositionsRecords::deleteOpposition($opposition);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function mass_selection_for_action( $request ): array{
        try {

            DB::beginTransaction();

            $information = ActionForMultipleRecordsService::actionForMultipleRecords($request->get('action'), $request->get('oppositions'));

            DB::commit();

            if (count($information) === 0) {
                $information[] = "No hay registros afectados";
            }

            return $information;

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function export_records( $request ): \Symfony\Component\HttpFoundation\BinaryFileResponse{
        if ($request->get('type') === 'pdf') {
            $domPDF = App::make('dompdf.wrapper');
            $oppositions = $this->model->query()->whereIn('id', $request->get('oppositions'))->get();
            $domPDF->loadView('resources.export.templates.pdf.oppositions', compact('oppositions'))->setPaper('a4', 'landscape')->setWarnings(false);
            return $domPDF->download('report-oppositions.pdf');
        }
        return Excel::download(new OppositionsExport($request->get('oppositions')), 'oppositions.'. $request->get('type'));
    }

    public function import_records( $request ): string{
        //Proceso de importacion con Queues - El archivo debe tener
        //(new OppositionsImport(Auth::user()))->import($request->file('oppositions'));

         /*
         // Lanzamiento de errores en caso de validacion sin uso de Queues
         if ($importInstance->failures()->isNotEmpty()) {
             throw ValidationException::withMessages([
                 'errors' => [
                     $importInstance->failures()
                 ]
             ]);
         }*/
        return "Proceso de importación iniciado";
    }

    public function get_relationship_syllabus($opposition)
    {
        $syllabus = [
            'opposition' => [
                'data' => $opposition,
                'items' => []
            ]
        ];

        foreach ($opposition->topics as $opposition_topic) {
            $subtopics = [];

            foreach ($opposition->subtopics as $opposition_subtopic) {
                $subtopics_id_by_topic = $opposition_topic->subtopics->pluck('id')->toArray();

                if (in_array($opposition_subtopic->getRouteKey(), $subtopics_id_by_topic, true)) {
                    $subtopics[] = $opposition_subtopic;
                }
            }

            $syllabus['opposition']['items'][] = [
                'topic' => $opposition_topic,
                'subtopics' => $subtopics
            ];
        }
        return $syllabus;

        /*return [
            'oppositions' => [
                'topics' => [
                    'data' => $opposition->topics
                ],
                'subtopics' => $opposition->subtopics
            ],
        ];*/
    }
}
