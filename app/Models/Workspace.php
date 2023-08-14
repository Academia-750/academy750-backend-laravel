<?php

namespace App\Models;

use App\Core\Resources\Storage\Storage;
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

    public static function deleteFromStorage($workspace)
    {
        // A folder can contain files uploaded to different providers
        $providers = Storage::all();
        $arrayLength = count($providers);

        // Using a for loop to iterate over the array of objects
        for ($i = 0; $i < $arrayLength; $i++) {
            $result = $providers[$i]->deleteFolder($workspace);
            if (isset($result['error'])) {
                return $result;
            }
        }
        return ['status' => 200];
    }
}