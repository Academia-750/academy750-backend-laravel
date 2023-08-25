<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupUsers extends Model
{
    use HasFactory;
    //use SoftDeletes;

    protected $fillable = [
        'user_id',
        'group_id',
        'discharged_at'
    ];

    public array $allowedIncludes = [];

    public array $adapterIncludes = [];

    // Relationships methods
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function uuid()
    {
        $this->user()->first()->uuid;
    }
}