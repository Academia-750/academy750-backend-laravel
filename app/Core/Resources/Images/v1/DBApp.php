<?php
namespace App\Core\Resources\Images\v1;

use App\Models\Image;
use App\Core\Resources\Images\v1\Interfaces\ImagesInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Core\Resources\Images\v1\Services\ActionForMultipleRecordsService;
use App\Core\Resources\Images\v1\Services\ActionsImagesRecords;
//use App\Imports\Api\Images\v1\ImagesImport;
use App\Exports\Api\Images\v1\ImagesExport;


class DBApp implements ImagesInterface
{
    protected Image $model;

    public function __construct(Image $image ){
        $this->model = $image;
    }

    public function index(){
        return $this->model->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function create( $request ): \App\Models\Image{
        try {

            DB::beginTransaction();
                $imageCreated = $this->model->query()->create([
                    '' => '',
                ]);
            DB::commit();

            return $this->model->applyIncludes()->find($imageCreated->id);

        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }

    }

    public function read( $image ): \App\Models\Image{
        return $this->model->applyIncludes()->find($image->getRouteKey());
    }

    public function update( $request, $image ): \App\Models\Image{
        try {

            DB::beginTransaction();
                $image->name = $request->get('name');
                $image->save();
            DB::commit();

            return $this->model->applyIncludes()->find($image->getRouteKey());

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function delete( $image ): void{
        try {

            DB::beginTransaction();
                //$image->delete();
                ActionsImagesRecords::deleteRecord( $image );
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            abort(500, $e->getMessage());
        }

    }

    public function action_for_multiple_records( $request ): array{
        try {

            DB::beginTransaction();

                $information = ActionForMultipleRecordsService::actionForMultipleRecords($request->get('action'), $request->get('images'));

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
            $images = $this->model->query()->whereIn('id', $request->get('images'))->get();
            $domPDF->loadView('resources.export.templates.pdf.images', compact('images'))->setPaper('a4', 'landscape')->setWarnings(false);
            return $domPDF->download('report-images.pdf');
        }
        return Excel::download(new ImagesExport($request->get('images')), 'images.'. $request->get('type'));
    }

    public function import_records( $request ): void{
        //Proceso de importacion con Queues - El archivo debe tener
        //(new ImagesImport(Auth::user()))->import($request->file('images'));
    }

}
