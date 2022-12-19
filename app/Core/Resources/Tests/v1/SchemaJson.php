<?php
namespace App\Core\Resources\Tests\v1;

use App\Http\Resources\Api\Questionnaire\v1\QuestionnaireCollection;
use App\Http\Resources\Api\Questionnaire\v1\QuestionnaireResource;
use App\Models\Test;
use App\Core\Resources\Tests\v1\Interfaces\TestsInterface;

use App\Core\Resources\Tests\v1\EventApp;
use Illuminate\Support\Str;

class SchemaJson implements TestsInterface
{
    protected EventApp $eventApp;

    public function __construct(EventApp $eventApp ){
        $this->eventApp = $eventApp;
    }

    public function index(): QuestionnaireCollection
    {
        return QuestionnaireCollection::make(
            $this->eventApp->index()
        );
    }

    public function read( $test ): QuestionnaireResource
    {
        return QuestionnaireResource::make(
            $this->eventApp->read( $test )
        );
    }

    public function generate( $request )
    {
        return QuestionnaireCollection::make(
            $this->eventApp->generate( $request )
        );
    }


}
