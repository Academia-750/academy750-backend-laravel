<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Core\Services\UUIDTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;


class Question extends Model
{
    use HasFactory;
    //use SoftDeletes;
    use UUIDTrait;

    const TEST = 1;
    const MEMORY_CARD = 2;

    protected $fillable = [
        "id",
        "question",
        "reason",
        "is_visible",
        "its_for_test",
        "its_for_card_memory"
    ];

    public array $allowedSorts = [
        "question-text",
        "created-at"
    ];

    public array $adapterSorts = [
        "question-text" => "QuestionText",
        "created-at" => "CreatedAt",
    ];

    public array $allowedFilters = [
        "search",
        "day",
        "month",
        "year",
        "date",
        "topic",
        "oppositions",
        "subtopics",
        "topic_group",
        "take",
        "type"
    ];

    public array $adapterFilters = [
        "search" => "Search",
        "day" => "Day",
        "month" => "Month",
        "year" => "Year",
        "date" => "Date",
        "topic" => "Topic",
        "oppositions" => "Oppositions",
        "subtopics" => "Subtopics",
        "topic_group" => "TopicGroup",
        "take" => "Take",
        "type" => "Type"
    ];

    public array $allowedIncludes = [
        'tests',
        'image',
        'questionable',
    ];

    public array $adapterIncludes = [
    ];

     protected $casts = [
        'id' => 'string'
     ];

    /* -------------------------------------------------------------------------------------------------------------- */
    // Sorts functions

    public function sortCreatedAt(Builder $query, $direction): void{
        $query->orderBy('created_at', $direction);
    }

    public function sortQuestionText (Builder $query, $direction): void {
        $query->orderBy('question', $direction);
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
        $query->where(static function($query) use ($value) {
            $query->where('question', 'LIKE' , "%{$value}%")
                ->orWhere('id', 'LIKE' , "%{$value}%")
                ->orWhere('reason', 'LIKE' , "%{$value}%");
        });
    }

    public function filterTake(Builder $query, $value): void{
        $query->take($value);
    }

    public function filterType(Builder $query, $value): void{
        switch ($value) {
            case self::TEST:
                $query->where('its_for_test', 'yes');
                break;
            case self::MEMORY_CARD:
                $query->where('its_for_card_memory', 'yes');
                break;
            default:
                break;
        }
    }

    public function filterTopic(Builder $query, $value): void{
            $values = Str::of($value)->explode(',');

            $query->whereHas('questionable.topic', function($query) use ($values) {
                if(count($values) > 0){
                    $query->where('id', '=', $values[0]);
                    for ($i = 0; $i < count($values); $i++) {
                        $query->orWhere('id', '=', $values[$i]);
                    }
                }else{
                    $query->where('id', '=', $values[0]);
                }
            });
    }

    public function filterOppositions(Builder $query, $value): void{
            $query->whereHas('questionable.topic.oppositions', function($query) use ($value) {
                $query->where('opposition_id', '=', $value);
            });
    }

    public function filterSubtopics(Builder $query, $value): void{
        $query->whereHas('questionable.topic.subtopics', function($query) use ($value) {
            $query->where('id', '=', $value);
        });
    }

    public function filterTopicGroup(Builder $query, $value): void{
        $query->whereHas('questionable.topic.topic_group', function($query) use ($value) {
            $query->where('topic_group_id', '=', $value);
        });
    }

    /* -------------------------------------------------------------------------------------------------------------- */
    // Scopes
    public function scopeIsVisible(): Builder
    {
        return $this->having('is_visible', 'yes');
    }

    /* -------------------------------------------------------------------------------------------------------------- */
     // Relationships methods

    public function questionable (): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo()->with(['topic.subtopics', 'topic.oppositions', 'topic.topic_group', 'topic.tests.test_type']);
    }

    public function answers (): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function answers_by_test (): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function tests (): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Test::class);
    }

    public function image (): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
