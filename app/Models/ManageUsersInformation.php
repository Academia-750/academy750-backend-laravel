<?php

namespace App\Models;

use App\Core\Services\UUIDTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageUsersInformation extends Model
{
    use HasFactory;

    protected $fillable = ['ip', 'has_accept_cookies'];

}
