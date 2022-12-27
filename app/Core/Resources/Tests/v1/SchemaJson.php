<?php
namespace App\Core\Resources\Tests\v1;

use App\Http\Resources\Api\Question\v1\QuestionCollection;
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

    public function fetch_unresolved_test( $test ): QuestionCollection
    {
        return QuestionCollection::make(
            $this->eventApp->fetch_unresolved_test( $test )
        )->additional([
            'meta' => [
                'test' => QuestionnaireResource::make($test)
            ]
        ]);
    }

    public function fetch_card_memory( $test ): QuestionCollection
    {
        return QuestionCollection::make(
            $this->eventApp->fetch_card_memory( $test )
        )->additional([
            'meta' => [
                'test' => QuestionnaireResource::make($test)
            ]
        ]);
    }

    public function create_a_quiz( $request )
    {
        return QuestionnaireResource::make(
            $this->eventApp->create_a_quiz( $request )
        );
    }


    public function get_cards_memory()
    {
        return QuestionnaireCollection::make(
            $this->eventApp->get_cards_memory()
        );
    }
}
