<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Lesson extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'description',
        'start_time',
        'end_time',
        'is_active',
        'is_online',
        'url'
    ];

}