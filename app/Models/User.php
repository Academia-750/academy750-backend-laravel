<?php

namespace App\Models;

use App\Core\Services\UserServiceTrait;
use App\Core\Services\UUIDTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 *
 * @property $id
 * @property $dni
 * @property $first_name
 * @property $last_name
 * @property $phone
 * @property $last_session
 * @property $state
 * @property $email
 * @property $email_verified_at
 *
 * @package App
 * @mixin Builder
 */

class User extends Authenticatable
{
    use HasApiTokens;
    use UUIDTrait;
    use UserServiceTrait;
    use Notifiable;
    use HasFactory;
    use HasRoles;

    public $keyType = "string";
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'dni',
        'first_name',
        'last_name',
        'phone',
        'last_session',
        'state',
        'email',
        'email_verified_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'state' => 'integer',
        'last_session' => 'datetime',
        'created_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    public array $allowedSorts = [
        'id',
        'dni',
        'first_name',
        'last_name',
        'phone',
        'last_session',
        'state',
        'email',
        'email_verified_at',
        "created-at"
    ];

    public array $adapterSorts = [
        'id' => 'ID',
        'dni' => 'DNI',
        'first_name' => 'FirstName',
        'last_name' => 'LastName',
        'phone' => 'Phone',
        'last_session' => 'LastSession',
        'state' => 'State',
        'email' => 'Email',
        'email_verified_at' => 'EmailVerifiedAt',
        "created-at" => "CreatedAt",
    ];

    public array $allowedFilters = [
        'id',
        'dni',
        'first_name',
        'last_name',
        'phone',
        'last_session',
        'state',
        'email',
        'email_verified_at',
        "search",
        "day",
        "month",
        "year",
        "date"
    ];

    public array $adapterFilters = [
        'id' => 'ID',
        'dni' => 'DNI',
        'first_name' => 'FirstName',
        'last_name' => 'LastName',
        'phone' => 'Phone',
        'last_session' => 'LastSession',
        'state' => 'State',
        'email' => 'Email',
        'email_verified_at' => 'EmailVerifiedAt',
        "search" => "Search",
        "day" => "Day",
        "month" => "Month",
        "year" => "Year",
        "date" => "Date",
    ];

    public array $allowedIncludes = [
        'roles',
        'roles-permissions'
    ];

    public array $adapterIncludes = [
        'roles-permissions' => 'roles.permissions'
    ];

    /* -------------------------------------------------------------------------------------------------------------- */
    // Sorts functions

    public function sortID (Builder $query, $direction): void {
        $query->orderBy('id', $direction);
    }
    public function sortDNI (Builder $query, $direction): void {
        $query->orderBy('dni', $direction);
    }
    public function sortFirstName (Builder $query, $direction): void {
        $query->orderBy('first_name', $direction);
    }
    public function sortLastName (Builder $query, $direction): void {
        $query->orderBy('last_name', $direction);
    }
    public function sortPhone (Builder $query, $direction): void {
        $query->orderBy('phone', $direction);
    }
    public function sortLastSession (Builder $query, $direction): void {
        $query->orderBy('last_session', $direction);
    }
    public function sortState (Builder $query, $direction): void {
        $query->orderBy('state', $direction);
    }
    public function sortEmail (Builder $query, $direction): void {
        $query->orderBy('email', $direction);
    }
    public function sortEmailVerifiedAt (Builder $query, $direction): void {
        $query->orderBy('email_verified_at', $direction);
    }

    public function sortCreatedAt(Builder $query, $direction): void{
        $query->orderBy('created_at', $direction);
    }

    /* -------------------------------------------------------------------------------------------------------------- */
    // Filters functions

    public function filterID (Builder $query, $value): void {
        $query->where('id', 'LIKE', "%{$value}%");
    }
    public function filterDNI (Builder $query, $value): void {
        $query->where('dni', 'LIKE', "%{$value}%");
    }
    public function filterFirstName (Builder $query, $value): void {
        $query->where('first_name', 'LIKE', "%{$value}%");
    }
    public function filterLastName (Builder $query, $value): void {
        $query->where('last_name', 'LIKE', "%{$value}%");
    }
    public function filterPhone (Builder $query, $value): void {
        $query->where('phone', 'LIKE', "%{$value}%");
    }
    public function filterLastSession (Builder $query, $value): void {
        $query->whereDate('last_session',$value);
    }
    public function filterState (Builder $query, $value): void {
        $query->where('state', 'LIKE', $value);
    }
    public function filterEmail (Builder $query, $value): void {
        $query->where('email', 'LIKE', "%{$value}%");
    }
    public function filterEmailVerifiedAt (Builder $query, $value): void {
        $query->where('email_verified_at', 'LIKE', "%{$value}%");
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
        $query->orWhere(function($query) use ($value) {
            $query->where('field', 'LIKE' , "%{$value}%")
                ->orWhere('other_field', 'LIKE' , "%{$value}%");
        });
    }

    /* -------------------------------------------------------------------------------------------------------------- */
    // Relationships methods

}
