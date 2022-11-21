<?php

namespace App\Models;

use App\Core\Services\UserServiceTrait;
use App\Core\Services\UUIDTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        'last_session' => 'date',
        'created_at' => 'date',
        'email_verified_at' => 'date',
    ];

    public array $allowedSorts = [
        'id',
        'dni',
        'first-name',
        'last-name',
        'phone',
        'last-session',
        'state-account',
        'email',
        'email-verified-at',
        "created-at"
    ];

    public array $adapterSorts = [
        'id' => 'ID',
        'dni' => 'DNI',
        'first-name' => 'FirstName',
        'last-name' => 'LastName',
        'phone' => 'Phone',
        'last-session' => 'LastSession',
        'state-account' => 'StateAccount',
        'email' => 'Email',
        'email-verified-at' => 'EmailVerifiedAt',
        "created-at" => "CreatedAt",
    ];

    public array $allowedFilters = [
        'id',
        'dni',
        'first-name',
        'last-name',
        'phone',
        'last-session',
        'created-at',
        'state-account',
        'email',
        'email-verified-at',
        "search",
        "role",
        "day",
        "month",
        "year",
        "date"
    ];

    public array $adapterFilters = [
        'id' => 'ID',
        'dni' => 'DNI',
        'first-name' => 'FirstName',
        'last-name' => 'LastName',
        'phone' => 'Phone',
        'last-session' => 'LastSession',
        'created-at' => 'CreatedAt',
        'state-account' => 'StateAccount',
        'email' => 'Email',
        'email-verified-at' => 'EmailVerifiedAt',
        "search" => "Search",
        "role" => "Role",
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
    public function sortStateAccount (Builder $query, $direction): void {
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
    public function filterCreatedAt (Builder $query, $value): void {
        $query->whereDate('created_at',$value);
    }
    public function filterStateAccount (Builder $query, $value): void {
        $query->where('state', $value);
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
            $query->where('dni', 'LIKE' , "%{$value}%")
                ->orWhere('first_name', 'LIKE' , "%{$value}%")
                ->orWhere('last_name', 'LIKE' , "%{$value}%")
                ->orWhere('phone', 'LIKE' , "%{$value}%")
                ->orWhere('phone', 'LIKE' , "%{$value}%")
                ->orWhere('email', 'LIKE' , "%{$value}%")
            ;
        });
    }

    public function filterRole(Builder $query, $value): void{
        $query->with("roles")->whereHas("roles", function($q) use ($value) {
            $q->whereIn("name", explode(',', $value));
        });
    }

    /* -------------------------------------------------------------------------------------------------------------- */
    // Relationships methods
    public function image (): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function tests (): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Test::class);
    }
}
