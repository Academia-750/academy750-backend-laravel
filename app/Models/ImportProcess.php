<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Core\Services\UUIDTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class ImportProcess extends Model
{
    use HasFactory;
    //use SoftDeletes;
    use UUIDTrait;

    protected $fillable = [
        'id',
        'name_file',
        'user_id',
        'total_number_of_records',
        'total_number_failed_records',
        'total_number_successful_records',
        'status_process_file',
        'category'
    ];

    public array $allowedSorts = [
        'name-file',
        'total-number-of-records',
        'total-number-failed-records',
        'total-number-successful-records',
        "created-at"
    ];

    public array $adapterSorts = [
        'name-file' => 'NameFile',
        'total-number-of-records' => 'TotalNumberOfRecords',
        'total-number-failed-records' => 'TotalNumberFailedRecords',
        'total-number-successful-records' => 'TotalNumberSuccessfulRecords',
        "created-at" => "CreatedAt",
    ];

    public array $allowedFilters = [
        'status_process_file',
        "search",
        "day",
        "month",
        "year",
        "date"
    ];

    public array $adapterFilters = [
        'status_process_file' => 'StatusProcessFile',
        "search" => "Search",
        "day" => "Day",
        "month" => "Month",
        "year" => "Year",
        "date" => "Date",
    ];

    public array $allowedIncludes = [];

    public array $adapterIncludes = [];

     protected $casts = [
        'uuid' => 'string',
         'name_file' => 'string',
         'user_id' => 'string',
         'total_number_of_records' => 'integer'
     ];

    /* -------------------------------------------------------------------------------------------------------------- */
    // Sorts functions

    public function sortNameFile(Builder $query, $direction): void{
        $query->orderBy('name_file', $direction);
    }

    public function sortTotalNumberOfRecords(Builder $query, $direction): void{
        $query->orderBy('total_number_of_records', $direction);
    }

    public function TotalNumberFailedRecords(Builder $query, $direction): void{
        $query->orderBy('total_number_failed_records', $direction);
    }

    public function TotalNumberSuccessfulRecords(Builder $query, $direction): void{
        $query->orderBy('total_number_successful_records', $direction);
    }

    public function sortCreatedAt(Builder $query, $direction): void{
        $query->orderBy('created_at', $direction);
    }

    /* -------------------------------------------------------------------------------------------------------------- */
    // Filters functions

    public function filterStatusProcessFile(Builder $query, $value): void{
        $query->where('status_process_file', 'LIKE' , "%{$value}%");
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
        $query->where(static function($query) use ($value) {
            $query->where('name_file', 'LIKE' , "%{$value}%");
        });
    }

    /* -------------------------------------------------------------------------------------------------------------- */
     // Relationships methods

    public function import_records (): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ImportRecord::class);
    }


}
