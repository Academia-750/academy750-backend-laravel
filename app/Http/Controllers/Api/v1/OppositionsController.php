<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Opposition;
use App\Core\Resources\Oppositions\v1\Interfaces\OppositionsInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Oppositions\CreateOppositionRequest;
use App\Http\Requests\Api\v1\Oppositions\UpdateOppositionRequest;
use App\Http\Requests\Api\v1\Oppositions\ActionForMassiveSelectionOppositionsRequest;
use App\Http\Requests\Api\v1\Oppositions\ImportOppositionsRequest;

/**
 * @group Oppositions
 *
 * APIs for managing oppositions
 */
class OppositionsController extends Controller
{
    protected $oppositionsInterface;

    public function __construct(OppositionsInterface $oppositionsInterface)
    {
        $this->oppositionsInterface = $oppositionsInterface;
    }

    public function index()
    {
        return $this->oppositionsInterface->index();
    }

    public function create(CreateOppositionRequest $request)
    {
        return $this->oppositionsInterface->create($request);
    }

    public function read(Opposition $opposition)
    {
        return $this->oppositionsInterface->read($opposition);
    }

    public function update(UpdateOppositionRequest $request, Opposition $opposition)
    {
        return $this->oppositionsInterface->update($request, $opposition);
    }

    public function delete(Opposition $opposition)
    {
        return $this->oppositionsInterface->delete($opposition);
    }

    public function mass_selection_for_action(ActionForMassiveSelectionOppositionsRequest $request): string
    {
        return $this->oppositionsInterface->mass_selection_for_action($request);
    }

    public function get_relationship_syllabus(Opposition $opposition)
    {
        return $this->oppositionsInterface->get_relationship_syllabus($opposition);
    }
}