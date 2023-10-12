<?php
namespace App\Http\Controllers\Api\v1;

use App\Models\Opposition;
use App\Core\Resources\Oppositions\v1\Interfaces\OppositionsInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Oppositions\CreateOppositionRequest;
use App\Http\Requests\Api\v1\Oppositions\UpdateOppositionRequest;
use App\Http\Requests\Api\v1\Oppositions\ActionForMassiveSelectionOppositionsRequest;
use App\Http\Requests\Api\v1\Oppositions\ImportOppositionsRequest;
use Illuminate\Http\Request;

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

    /**
     * Opposition: List
     *
     * List of available opposition
     * @authenticated
     */
    public function index(Request $request)
    {
        return $this->oppositionsInterface->index($request);
    }

    /**
     * Opposition: Create
     *
     * Create a new Opposition
     * @authenticated
     */
    public function create(CreateOppositionRequest $request)
    {
        return $this->oppositionsInterface->create($request);
    }

    /**
     * Opposition: Info
     *
     * Single Opposition Information
     * @authenticated
     */
    public function read(Opposition $opposition)
    {
        return $this->oppositionsInterface->read($opposition);
    }


    /**
     * Opposition: Update
     *
     * Update an opposition
     * @authenticated
     */
    public function update(UpdateOppositionRequest $request, Opposition $opposition)
    {
        return $this->oppositionsInterface->update($request, $opposition);
    }

    /**
     * Opposition: Delete
     *
     * Delete an oppositionc
     * @authenticated
     */
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