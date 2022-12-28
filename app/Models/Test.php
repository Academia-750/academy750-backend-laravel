<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Core\Services\UUIDTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use function Symfony\Component\String\s;

class Test extends Model
{
    use HasFactory;
    //use SoftDeletes;
    use UUIDTrait;

    public $keyType = "string";
    public $incrementing = false;

    protected $fillable = [
        'id',
        "number_of_questions_requested",
        "number_of_questions_generated",
        "test_result",
        "total_questions_correct",
        "total_questions_wrong",
        "total_questions_unanswered",
        "is_solved_test",
        "test_type",
        "opposition_id",
        "user_id"
    ];

    public array $allowedSorts = [
        "test-questions-count",
        "created-at"
    ];

    public array $adapterSorts = [
        "test-questions-count" => "TestQuestionsCount",
        "created-at" => "CreatedAt",
    ];

    public array $allowedFilters = [
        "search",
        "day",
        "month",
        "year",
        "date"
    ];

    public array $adapterFilters = [
        "search" => "Search",
        "day" => "Day",
        "month" => "Month",
        "year" => "Year",
        "date" => "Date",
    ];

    public array $allowedIncludes = [
        'opposition',
        'user',
        'questions',
        'topics',
        'subtopics',
    ];

    public array $adapterIncludes = [];

     protected $casts = [
        'id' => 'string'
     ];

    /* -------------------------------------------------------------------------------------------------------------- */
    // Sorts functions

    public function sortTestQuestionsCount(Builder $query, $direction): void{
        $query->orderBy('number_of_questions_generated', $direction);
    }

    public function sortCreatedAt(Builder $query, $direction): void{
        $query->orderBy('created_at', $direction);
    }

    /* -------------------------------------------------------------------------------------------------------------- */
    // Filters functions

    public function filterYear(Builder $query, $value): void{
        $query->whereYear('created_at', $value);
    }
    public function filterMonth(Builder $query, $value): void{
        $query->whereMonth('created_at', $value);
    }
    public function filterDay(Builder $query, $value): void{
        $query->whereDay('created_at', $value);
    }
    public function filterDate(Builder $query, $value): void{
        $query->whereDate('created_at', $value);
    }

    public function filterSearch(Builder $query, $value): void{
        $query->orWhere(static function($query) use ($value) {
            $query->whereDate('created_at', $value)
                ->orWhere('number_of_questions_requested', '=', $value)
                ->orWhere('number_of_questions_generated', '=', $value);
        });
    }

    /* -------------------------------------------------------------------------------------------------------------- */
     // Relationships methods

    /*public function test_type (): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TestType::class);
    }*/

    public function opposition (): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Opposition::class);
    }

    public function user (): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function questions (): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Question::class)
            ->withPivot([
                'have_been_show_test',
                'have_been_show_card_memory',
                'answer_id',
                'status_solved_question',
                'test_id',
                'question_id',
            ]);
    }

    public function topics (): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Topic::class, 'testable')
            ->withTimestamps();
    }

    public function subtopics (): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Subtopic::class, 'testable')
            ->withTimestamps();
    }
}
