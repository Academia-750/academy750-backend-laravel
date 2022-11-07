<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Opposition;
use App\Core\Resources\Oppositions\v1\Interfaces\OppositionsInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\v1\Oppositions\CreateOppositionRequest;
use App\Http\Requests\Api\v1\Oppositions\UpdateOppositionRequest;
use App\Http\Requests\Api\v1\Oppositions\ActionForMassiveSelectionOppositionsRequest;
use App\Http\Requests\Api\v1\Oppositions\ExportOppositionsRequest;
use App\Http\Requests\Api\v1\Oppositions\ImportOppositionsRequest;

class OppositionsController extends Controller
{
    protected $oppositionsInterface;

    public function __construct(OppositionsInterface $oppositionsInterface ){
        $this->oppositionsInterface = $oppositionsInterface;
    }

    public function index(){
        return $this->oppositionsInterface->index();
    }

    public function create(CreateOppositionRequest $request){
        return $this->oppositionsInterface->create($request);
    }

    public function read(Opposition $opposition){
        return $this->oppositionsInterface->read( $opposition );
    }

    public function update(UpdateOppositionRequest $request, Opposition $opposition){
        return $this->oppositionsInterface->update( $request, $opposition );
    }

    public function delete(Opposition $opposition){
        return $this->oppositionsInterface->delete( $opposition );
    }

    public function mass_selection_for_action(ActionForMassiveSelectionOppositionsRequest $request): string{
        return $this->oppositionsInterface->mass_selection_for_action( $request );
    }

    public function export_records(ExportOppositionsRequest $request){
        return $this->oppositionsInterface->export_records( $request );
    }

    public function import_records(ImportOppositionsRequest $request){
        return $this->oppositionsInterface->import_records( $request );
    }

    public function download_template_import_records (): \Symfony\Component\HttpFoundation\StreamedResponse {
        return Storage::disk('public')->download('templates_import/opposition.csv', 'template_import_opposition');
    }
}
