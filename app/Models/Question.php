<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Core\Services\UUIDTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Question extends Model
{
    use HasFactory;
    //use SoftDeletes;
    use UUIDTrait;

    protected $fillable = [
        "id",
        "question",
        "reason",
        "is_visible",
        "its_for_test",
        "its_for_card_memory",
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
        'answers',
        'tests',
        'image'
    ];

    public array $adapterIncludes = [];

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
                ->orWhere('reason', 'LIKE' , "%{$value}%");
        });
    }

    /* -------------------------------------------------------------------------------------------------------------- */
     // Relationships methods

    public function questionable (): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function answers (): \Illuminate\Database\Eloquent\Relations\HasMany
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
