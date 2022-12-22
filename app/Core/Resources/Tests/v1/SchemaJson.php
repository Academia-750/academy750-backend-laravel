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

    public function get_tests_unresolved(): QuestionnaireCollection
    {
        return QuestionnaireCollection::make(
            $this->eventApp->get_tests_unresolved()
        );
    }

    public function fetch_unresolved_test( $test ): QuestionnaireResource
    {
        return QuestionnaireResource::make(
            $this->eventApp->fetch_unresolved_test( $test )
        );
    }

    public function create_a_quiz( $request )
    {
        return QuestionnaireResource::make(
            $this->eventApp->create_a_quiz( $request )
        );
    }


}
