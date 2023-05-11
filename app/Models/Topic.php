<?php

namespace App\Models;

use App\Core\Services\StateAvailableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Core\Services\UUIDTrait;
use Illuminate\Database\Eloquent\Builder;

class Topic extends Model
{
    use HasFactory;
    use StateAvailableTrait;
    use UUIDTrait;

    protected $fillable = [
        "id",
        "name",
        "topic_group_id",
        "is_available"
    ];

    public array $allowedSorts = [
        "name",
        "created-at"
    ];

    public array $adapterSorts = [
        "name" => "Name",
        "created-at" => "CreatedAt",
    ];

    public array $allowedFilters = [
        "search",
        "day",
        "month",
        "year",
        "date",
        "oppositions",
        "topic-group",
        "has-questions-available",
    ];

    public array $adapterFilters = [
        "search" => "Search",
        "day" => "Day",
        "month" => "Month",
        "year" => "Year",
        "date" => "Date",
        "oppositions" => "Oppositions",
        "topic-group" => "TopicGroup",
        "has-questions-available" => "HasQuestionsAvailable",
    ];

    public array $allowedIncludes = [
        'topic-group'
    ];

    public array $adapterIncludes = [
        'topic-group' => 'topic_group'
    ];

     protected $casts = [
        'uuid' => 'string'
     ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /* -------------------------------------------------------------------------------------------------------------- */
    // Sorts functions

    public function sortName(Builder $query, $direction): void{
        $query->orderBy('name', $direction);
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
        $query->where(static function($query) use ($value) {
            $query->where('name', 'LIKE' , "%{$value}%")
                ->orWhereHas('topic_group', static function(Builder $query) use ($value) {
                    $query->where('name', 'like', "%{$value}%")
                        ->orWhere('id', 'LIKE' , "%{$value}%");
                })
            ;
        });
    }

    public function filterOppositions(Builder $query, $value): void{
        $query->whereHas('oppositions', function($query) use ($value) {
            $query->where('opposition_id', '=', $value);
        });
    }

    public function filterTopicGroup(Builder $query, $value): void{
        $query->whereHas('topic_group', function($query) use ($value) {
            $query->where('topic_group_id', '=', $value);
        });
    }

    public function filterTopicsAvailable (): void {

    }

    public function filterHasQuestionsAvailable (Builder $query) {
        return $query
            ->where(static function ($query) {
                $query->whereHas('questions', static function($query) {
                    $query
                        ->where('is_visible', '=', 'yes');
                })->whereHas('subtopics.questions', static function($query) {
                    $query->where('is_visible', '=', 'yes');
                });


            } ,'>' , 0);

    }

    public function questionsCount(): int
    {
        $count = $this->questions()->where('is_visible', '=', 'yes')->count();
        foreach ($this->subtopics as $subtopic) {
            $count += $subtopic->questions()->where('is_visible', '=', 'yes')->count();
        }

        return $count;
    }

    /* -------------------------------------------------------------------------------------------------------------- */
     // Relationships methods

    public function subtopics (): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subtopic::class);
    }

    public function oppositions (): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Opposition::class, 'oppositionable')
            ->withPivot('is_available')
            ->withTimestamps();
    }

    public function tests (): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Test::class, 'testable')
            ->withTimestamps();
    }

    public function topic_group (): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TopicGroup::class);
    }

    public function questions (): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        /*return $this->morphOne(Question::class, 'questionable')->ofMany([
            'id',
            'question',
            'reason',
            'is_available',
            'subtopic_id'
        ], static function ($query) {
            $query->whereNull('subtopic_id');
        });*/

        return $this->morphMany(Question::class, 'questionable');
    }
}
