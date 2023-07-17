<?php

namespace App\Models;

use App\Core\Services\StateAvailableTrait;
use App\Core\Services\UUIDTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Opposition extends Model
{
    use HasFactory;
    use UUIDTrait;
    use StateAvailableTrait;

    protected $fillable = [
        'id',
        'name',
        'period',
        'is_available'
    ];

    public array $allowedSorts = [
        'name',
        'period',
        "created-at"
    ];

    public array $adapterSorts = [
        'name' => "Name",
        'period' => "Period",
        "created-at" => "CreatedAt",
    ];

    public array $allowedFilters = [
        "name",
        "period",
        "created-at",
        "search",
        "day",
        "month",
        "year",
        "date"
    ];

    public array $adapterFilters = [
        "name" => "Name",
        "period" => "Period",
        "created-at" => "CreatedAt",
        "search" => "Search",
        "day" => "Day",
        "month" => "Month",
        "year" => "Year",
        "date" => "Date",
    ];

    public array $allowedIncludes = [];

    public array $adapterIncludes = [];

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

    public function sortPeriod(Builder $query, $direction): void{
        $query->orderBy('period', $direction);
    }

    public function sortCreatedAt(Builder $query, $direction): void{
        $query->orderBy('created_at', $direction);
    }

    /* -------------------------------------------------------------------------------------------------------------- */
    // Filters functions

    public function filterName(Builder $query, $value): void{
        $query->where('name', 'LIKE', "%{$value}%");
    }
    public function filterPeriod(Builder $query, $value): void{
        $query->where('period', 'LIKE', "%{$value}%");
    }
    public function filterCreatedAt (Builder $query, $value): void {
        $query->whereDate('created_at',$value);
    }
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
        $query->where(function($query) use ($value) {
            $query->where('name', 'LIKE' , "%{$value}%")
                //->orWhere('id', 'LIKE' , "%{$value}%")
                ->orWhere('period', 'LIKE' , "%{$value}%");
        });
    }

    /* -------------------------------------------------------------------------------------------------------------- */
     // Relationships methods

    public function topics (): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Topic::class, 'oppositionable')
            ->withPivot('is_available')
            ->withTimestamps();
    }

    public function subtopics (): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Subtopic::class, 'oppositionable')
            ->withPivot('is_available')
            ->withTimestamps();
    }

    public function tests (): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Test::class);
    }
}
