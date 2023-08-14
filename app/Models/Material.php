<?php

namespace App\Models;

use App\Core\Resources\Storage\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Material extends Model
{

    use HasFactory;

    public static function allowedTypes()
    {
        return ['material', 'recording'];
    }


    protected $fillable = [
        'name',
        'type',
        'url',
        'tags',
        'workspace_id'
    ];

    protected $attributes = [
        'tags' => '',
        'url' => ''
    ];


    // Relationships methods
    public function workspace()
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    public static function deleteFromStorage($material)
    {
        if (!$material->url) {
            return ['status' => 204, 'message' => 'No Action'];
        }

        if ($material->type !== 'material') {
            return ['status' => 204, 'message' => 'No Action'];
        }

        return Storage::for($material)->deleteFile($material); // Delete the old one.
    }

}