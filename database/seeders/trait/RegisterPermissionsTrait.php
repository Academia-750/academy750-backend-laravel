<?php
namespace Database\Seeders\trait;

use App\Core\Services\UuidGeneratorService;
use App\Models\Permission;

trait RegisterPermissionsTrait
{
    public function existsPermission ($permission_name): bool {
        return Permission::query()->where('name', '=', $permission_name)->first() !== null;
    }

    public function registerPermission ($permissionKey, $aliasName)
    {
        if (!$this->existsPermission($permissionKey)) {
            Permission::query()->create([
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => $permissionKey,
                'alias_name' => $aliasName,
            ]);
        }
    }

    public function permissionsUsersByRoleStudent (): void {
        $this->admin->refresh();

        $permissions = [
            /* keyName = aliasName */
            'list-students' => 'list-students',
            'see-a-student' => 'see-a-student',
            'create-student' => 'create-student',
            'edit-student' => 'edit-student',
            'delete-student' => 'delete-student',
            'action-for-multiple-users' => 'action-for-multiple-users',
            'export-students' => 'export-students',
            'import-students' => 'import-students',
            'enable-account-student' => 'enable-account-student',
            'disable-account-student' => 'disable-account-student',
        ];

        foreach ($permissions as $keyName => $aliasName) {
            $this->registerPermission($keyName, $aliasName);
        }

        $this->admin->givePermissionTo(
            array_keys($permissions)
        );
    }

    public function permissionsOppositions (): void {
        $this->admin->refresh();
        $permissions = [
            'list-oppositions' => 'list-oppositions',
            'see-a-opposition' => 'see-a-opposition',
            'create-opposition' => 'create-opposition',
            'edit-opposition' => 'edit-opposition',
            'delete-opposition' => 'delete-opposition',
            'action-for-multiple-oppositions' => 'action-for-multiple-oppositions',
            'see-syllabus' => 'see-syllabus',
            'add-topic-to-opposition' => 'add-topic-to-opposition',
            'remove-topic-of-opposition' => 'remove-topic-of-opposition',
        ];

        foreach ($permissions as $keyName => $aliasName) {
            $this->registerPermission($keyName, $aliasName);
        }

        $this->admin->givePermissionTo(
            array_keys($permissions)
        );
    }

    public function permissionsTopics (): void {
        $this->admin->refresh();
        $permissions = [
            'list-topic-groups' => 'list-topic-groups',
            'add-topic-to-opposition' => 'add-topic-to-opposition',
            'remove-topic-of-opposition' => 'remove-topic-of-opposition',
            'list-topics' => 'list-topics',
            'see-a-topic' => 'see-a-topic',
            'create-topic' => 'create-topic',
            'edit-topic' => 'edit-topic',
            'delete-topic' => 'delete-topic',
            'action-for-multiple-topics' => 'action-for-multiple-topics',
            'import-topics' => 'import-topics',
        ];

        foreach ($permissions as $keyName => $aliasName) {
            $this->registerPermission($keyName, $aliasName);
        }

        $this->admin->givePermissionTo(
            array_keys($permissions)
        );
    }

    public function permissionsResourceTopic (): void {
        $this->admin->refresh();
        $permissions = [
            'see-oppositions' => 'see-oppositions',
            'see-questions' => 'see-questions',
            'add-subtopic-to-topic' => 'add-subtopic-to-topic',
            'edit-subtopic-of-topic' => 'edit-subtopic-of-topic',
            'remove-subtopic-of-topic' => 'remove-subtopic-of-topic',
            'see-questions-of-subtopic' => 'see-questions-of-subtopic',
            'add-opposition-to-topic' => 'add-opposition-to-topic',
            'remove-opposition-of-topic' => 'remove-opposition-of-topic',
            'add-question-to-topic' => 'add-question-to-topic',
            'see-question-of-topic' => 'see-question-of-topic',
            'remove-question-of-topic' => 'remove-question-of-topic',
        ];

        foreach ($permissions as $keyName => $aliasName) {
            $this->registerPermission($keyName, $aliasName);
        }

        $this->admin->givePermissionTo(
            array_keys($permissions)
        );
    }

    public function permissionSubtopics (): void {
        $this->admin->refresh();
        $permissions = [
            'list-subtopics' => 'list-subtopics',
            'see-a-subtopic' => 'see-subtopic',
            'create-subtopic' => 'create-subtopic',
            'edit-subtopic' => 'edit-subtopic',
            'delete-subtopic' => 'delete-subtopic',
            'action-for-multiple-subtopics' => 'action-for-multiple-subtopics',
        ];

        foreach ($permissions as $keyName => $aliasName) {
            $this->registerPermission($keyName, $aliasName);
        }

        $this->admin->givePermissionTo(
            array_keys($permissions)
        );
    }

    public function permissionsTestsStudent (): void {
        $this->student->refresh();

        $permissions = [
            'create-tests-for-resolve' => 'create-tests-for-resolve',
            'list-oppositions' => 'list-oppositions',
            'list-topics' => 'list-topics',
            'list-topic-groups' => 'list-topic-groups',
            'list-uncompleted-tests' => 'list-uncompleted-tests',
            'resolve-a-tests' => 'resolve-a-tests',
            'see-results-of-tests' => 'see-results-of-tests',
            'see-my-information-like-student' => 'see-my-information-like-student',
            'see-my-history-results-questions-of-all-tests' => 'see-my-history-results-questions-of-all-tests',
        ];

        foreach ($permissions as $keyName => $aliasName) {
            $this->registerPermission($keyName, $aliasName);
        }

        $this->student->givePermissionTo(
            array_keys($permissions)
        );
    }
}
