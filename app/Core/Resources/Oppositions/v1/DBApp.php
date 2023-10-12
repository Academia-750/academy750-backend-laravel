<?php
namespace App\Core\Resources\Oppositions\v1;

use App\Core\Resources\Oppositions\v1\Services\ActionForMultipleRecordsService;
use App\Core\Resources\Oppositions\v1\Services\ActionsOppositionsRecords;
use App\Models\Opposition;
use App\Core\Resources\Oppositions\v1\Interfaces\OppositionsInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;


class DBApp implements OppositionsInterface
{
    protected $model;

    public function __construct(Opposition $opposition)
    {
        $this->model = $opposition;
    }

    public function index($request)
    {

        // This need to be refactor in 2 APIS for admin and for user
        if ($request->user()->hasRole('admin')) {
            return $this->model::applyFilters()
                ->applySorts()
                ->where('is_available', 'yes')
                ->applyIncludes()
                ->jsonPaginate();
        }

        $allowedOppositions = DB::select(
            'call get_available_oppositions_by_user(?)',
            array(
                $request->user()->id
            )
        );

        // Cast STD to Array
        $oppositionIds = array_map(function ($item) {
            return ((array) $item)['opposition_id'];
        }, $allowedOppositions);


        return $this->model::applyFilters()
            ->applySorts()
            ->whereIn('id', $oppositionIds)
            ->where('is_available', 'yes')
            ->applyIncludes()
            ->jsonPaginate();

    }

    public function create($request): \App\Models\Opposition
    {
        try {

            DB::beginTransaction();
            $oppositionCreated = $this->model->query()->create([
                'name' => $request->get('name'),
                'period' => $request->get('period'),
                'is_available' => 'yes'
            ]);
            DB::commit();

            return $this->model->applyIncludes()->findOrFail($oppositionCreated->getKey());

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function read($opposition): \App\Models\Opposition
    {
        return $this->model->applyIncludes()->findOrFail($opposition->getKey());
    }

    public function update($request, $opposition): \App\Models\Opposition
    {
        try {

            DB::beginTransaction();
            $opposition->name = $request->get('name') ?? $opposition->name;
            $opposition->period = $request->get('period') ?? $opposition->period;
            $opposition->save();
            DB::commit();

            return $this->model->applyIncludes()->findOrFail($opposition->getKey());

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function delete($opposition): void
    {
        try {

            DB::beginTransaction();
            ActionsOppositionsRecords::deleteOpposition($opposition);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function mass_selection_for_action($request): array
    {
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

                if (in_array($opposition_subtopic->getKey(), $subtopics_id_by_topic, true)) {
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