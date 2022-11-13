<?php
namespace App\Core\Resources\Images\v1;

use App\Models\Image;
use App\Core\Resources\Images\v1\Interfaces\ImagesInterface;
use App\Http\Resources\Api\Image\v1\ImageCollection;
use App\Http\Resources\Api\Image\v1\ImageResource;
use App\Core\Resources\Images\v1\EventApp;
use Illuminate\Support\Str;

class SchemaJson implements ImagesInterface
{
    protected EventApp $eventApp;

    public function __construct(EventApp $eventApp ){
        $this->eventApp = $eventApp;
    }

    public function index(): ImageCollection
    {
        return ImageCollection::make(
            $this->eventApp->index()
        );
    }

    public function create( $request ): \Illuminate\Http\JsonResponse
    {
        return ImageResource::make($this->eventApp->create($request))
                    ->response()
                    ->setStatusCode(201);
    }

    public function read( $image ): ImageResource
    {
        return ImageResource::make(
            $this->eventApp->read( $image )
        );
    }

    public function update( $request, $image ): ImageResource
    {
        return ImageResource::make(
            $this->eventApp->update( $request, $image )
        );
    }

    public function delete( $image ): \Illuminate\Http\Response
    {
        $this->eventApp->delete( $image );
        return response()->noContent();
    }

    public function action_for_multiple_records( $request ): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'information' => $this->eventApp->action_for_multiple_records( $request )
        ], 200);
    }

    public function export_records( $request ): \Illuminate\Http\JsonResponse
    {
        $this->eventApp->export_records( $request );

        return response()->json([
            'message' => "Proceso de exportación iniciada"
        ], 200);
    }

    public function import_records( $request ): \Illuminate\Http\JsonResponse
    {
        $this->eventApp->import_records( $request );

        return response()->json([
            'message' => "Proceso de importación iniciada"
        ], 200);
    }

}
