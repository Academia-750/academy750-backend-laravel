<?php

namespace App\Models;

use App\Core\Resources\Storage\Storage;
use App\Core\Resources\Watermark\Watermark;
use DocumentType;
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

    // Relationships methods
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class)->withTimestamps();
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


    public function canDownload(User $user)
    {

        if ($this->type === 'recording') {
            return $user->can(Permission::SEE_LESSON_RECORDINGS);
        }

        return $user->can(Permission::SEE_LESSON_MATERIALS);
    }
    public function downloadUrl(User $user)
    {

        if ($this->type === 'recording') {
            return $this->url;
        }

        // else $this->type === 'material'

        $docType = $this->getDocumentTypeFromURL();

        if ($docType === DocumentType::PDF) {
            return Watermark::get()->pdf($this->url, $this->name, $user);
        }
        if ($docType === DocumentType::IMAGE) {
            return Watermark::get()->image($this->url, $this->name, $user);
        }

        // Other Doc type
        return $this->url;
    }




    public function getDocumentTypeFromURL()
    {
        return getDocumentTypeFromURL($this->url);
    }
}