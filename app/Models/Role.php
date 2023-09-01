<?php

namespace App\Models;

use App\Core\Services\UUIDTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as RoleSpatieModel;

class Role extends RoleSpatieModel
{
    use HasFactory;

    use UUIDTrait;
    public $keyType = "string";
    protected $primaryKey = 'id';
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = [
        "id",
        'name',
        'alias_name',
        'guard_name',
        'default_role',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public static function parseName($name)
    {
        return strtr(Str::lower($name), [
            ' ' => '_',
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ñ' => 'n'
        ]);
    }

}