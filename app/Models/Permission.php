<?php

namespace App\Models;

use App\Core\Services\UUIDTrait;
use Spatie\Permission\Models\Permission as PermissionSpatieModel;



/**
 * In order to define a new permission
 * follow instructions on PermissionSeeder
 */
class Permission extends PermissionSpatieModel
{

    /**
     * Lessons
     **/
    public const SEE_LESSONS = 'see-lessons'; // Only allow to see your lessons but nothing else
    public const JOIN_LESSONS = 'join-lessons'; // Allows confirm your participation to a lesson
    public const SEE_ONLINE_LESSON = 'online-lessons'; // Allows you to access the online lessons page
    public const SEE_LESSON_MATERIALS = 'material-lessons'; // Allows you to access lessons materials type material
    public const SEE_LESSON_RECORDINGS = 'recording-lessons'; // Allows you to access lessons materials type recordings
    public const SEE_LESSON_PARTICIPANTS = 'participants-lessons'; // Allows you to see the list of participants

    /**
     * Opposition Tests
     */
    public const GENERATE_TESTS = 'tests'; // Allows you to see the list of participants

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
    ];

    protected $casts = [
        'id' => 'string'
    ];

}