<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Material extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'url',
        'tags',
    ];

    protected $attributes = [
        'tags' => ''
    ];


    // Relationships methods
    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

}