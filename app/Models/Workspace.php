<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Workspace extends Model
{
    use HasFactory;

    protected $attributes = [
        'tags' => ''
    ];

    protected $fillable = [
        'name',
        'type',
        'tags'
    ];

    // Relationships methods
    public function materials()
    {
        return $this->hasMany(Material::class);
    }
}