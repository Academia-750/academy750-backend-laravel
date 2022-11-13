<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Image;
use App\Core\Resources\Images\v1\Interfaces\ImagesInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\v1\Images\CreateImageRequest;
use App\Http\Requests\Api\v1\Images\UpdateImageRequest;
use App\Http\Requests\Api\v1\Images\ActionForMassiveSelectionImagesRequest;
use App\Http\Requests\Api\v1\Images\ExportImagesRequest;
use App\Http\Requests\Api\v1\Images\ImportImagesRequest;

class ImagesController extends Controller
{
    protected ImagesInterface $imagesInterface;

    public function __construct(ImagesInterface $imagesInterface ){
        $this->imagesInterface = $imagesInterface;
    }

    public function index(){
        return $this->imagesInterface->index();
    }

    public function create(CreateImageRequest $request){
        return $this->imagesInterface->create($request);
    }

    public function read(Image $image){
        return $this->imagesInterface->read( $image );
    }

    public function update(UpdateImageRequest $request, Image $image){
        return $this->imagesInterface->update( $request, $image );
    }

    public function delete(Image $image){
        return $this->imagesInterface->delete( $image );
    }

    public function action_for_multiple_records(ActionForMassiveSelectionImagesRequest $request): string{
        return $this->imagesInterface->action_for_multiple_records( $request );
    }

    public function export_records(ExportImagesRequest $request){
        return $this->imagesInterface->export_records( $request );
    }

    public function import_records(ImportImagesRequest $request){
        return $this->imagesInterface->import_records( $request );
    }

    public function download_template_import_records (): \Symfony\Component\HttpFoundation\StreamedResponse {
        return Storage::disk('public')->download('templates_import/image.csv', 'template_import_image');
    }
}
