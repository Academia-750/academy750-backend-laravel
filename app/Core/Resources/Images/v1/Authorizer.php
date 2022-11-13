<?php
namespace App\Core\Resources\Images\v1;

use App\Models\Image;
use App\Core\Resources\Images\v1\Interfaces\ImagesInterface;
use App\Http\Resources\Api\Image\v1\ImageCollection;
use App\Http\Resources\Api\Image\v1\ImageResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Core\Resources\Images\v1\SchemaJson;
class Authorizer implements ImagesInterface
{
    protected SchemaJson $schemaJson;

    public function __construct(SchemaJson $schemaJson ){
        $this->schemaJson = $schemaJson;
    }

    public function index(): ImageCollection
    {
        Gate::authorize('index', Image::class );
        return $this->schemaJson->index();
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('create', Image::class );
        return $this->schemaJson->create($request);
    }

    public function read( $image ): ImageResource
    {
        Gate::authorize('read', $image );
        return $this->schemaJson->read( $image );
    }

    public function update( $request, $image ): ImageResource
    {
        Gate::authorize('update', $image );
        return $this->schemaJson->update( $request, $image );
    }

    public function delete( $image ): \Illuminate\Http\Response
    {
        Gate::authorize('delete', $image );
        return $this->schemaJson->delete( $image );
    }

    public function action_for_multiple_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('mass_selection_for_action', Image::class );
        return $this->schemaJson->action_for_multiple_records( $request );
    }

    public function export_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('export_records', Image::class );
        return $this->schemaJson->export_records( $request );
    }

    public function import_records( $request ): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('import_records', Image::class );
        return $this->schemaJson->import_records( $request );
    }

}
