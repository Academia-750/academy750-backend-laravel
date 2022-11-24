<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Core\Services\UUIDTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Subtopic extends Model
{
    use HasFactory;
    //use SoftDeletes;
    use UUIDTrait;

    protected $fillable = [
        "id",
        "name",
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
        'topics',
        'oppositions',
        'questions',
    ];

    public array $adapterIncludes = [];

     protected $casts = [
        'id' => 'string'
     ];

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
        $query->orWhere(static function($query) use ($value) {
            $query->where('name', 'LIKE' , "%{$value}%")
                ->orWhere('is_available', 'LIKE' , "%{$value}%");
        });
    }

    /* -------------------------------------------------------------------------------------------------------------- */
     // Relationships methods
    public function topic (): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }


    public function oppositions (): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Opposition::class, 'oppositionable')
            ->withPivot('is_available')
            ->withTimestamps();
    }


    public function questions (): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Question::class, 'questionable');
        //return $this->hasMany(Question::class);
    }

}
