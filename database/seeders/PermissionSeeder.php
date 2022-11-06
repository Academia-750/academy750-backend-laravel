<?php

namespace Database\Seeders;

use App\Core\Services\UuidGeneratorService;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'list-students',
                'alias_name' => 'list-students',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'see-a-student',
                'alias_name' => 'see-a-student',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'create-student',
                'alias_name' => 'create-student',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'edit-student',
                'alias_name' => 'edit-student',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'delete-student',
                'alias_name' => 'delete-student',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'action-for-multiple-users',
                'alias_name' => 'Realizar acciones sobre múltiples Usuarios',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'lock-account-a-user',
                'alias_name' => 'Deshabilitar la cuenta de un usuario',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'unlock-account-a-user',
                'alias_name' => 'Habilitar la cuenta de un usuario',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'export-students',
                'alias_name' => 'export-students',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'import-students',
                'alias_name' => 'import-students',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'enable-account-student',
                'alias_name' => 'enable-account-student',
            ])
        ; // Dar de baja
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'disable-account-student',
                'alias_name' => 'disable-account-student',
            ])
        ; // reactivar alumno al sistema

        // Permisos: Gestion de Oposiciones
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'list-oppositions',
                'alias_name' => 'list-oppositions',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'create-opposition',
                'alias_name' => 'create-opposition',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'edit-opposition',
                'alias_name' => 'edit-opposition',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'delete-opposition',
                'alias_name' => 'delete-opposition',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'see-syllabus',
                'alias_name' => 'see-syllabus',
            ])
        ; // ver temario
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'add-topic-to-opposition',
                'alias_name' => 'add-topic-to-opposition',
            ])
        ; // Agregar tema a la oposicion
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'remove-topic-of-opposition',
                'alias_name' => 'remove-topic-of-opposition',
            ])
        ; // Remover o eliminar un tema de la oposicion

        // Permisos: Gestion de temas
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'list-topics',
                'alias_name' => 'list-topics',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'create-topic',
                'alias_name' => 'create-topic',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'edit-topic',
                'alias_name' => 'edit-topic',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'delete-topic',
                'alias_name' => 'delete-topic',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'import-topics',
                'alias_name' => 'import-topics',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'see-subtopics',
                'alias_name' => 'see-subtopics',
            ])
        ; // ver subtemas
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'see-oppositions',
                'alias_name' => 'see-oppositions',
            ])
        ; // ver oposiciones
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'see-questions',
                'alias_name' => 'see-questions',
            ])
        ; // ver preguntas

        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'add-subtopic-to-topic',
                'alias_name' => 'add-subtopic-to-topic',
            ])
        ; // Agregar un subtema al tema
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'edit-subtopic-of-topic',
                'alias_name' => 'edit-subtopic-of-topic',
            ])
        ; // Editar subtema del tema
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'remove-subtopic-of-topic',
                'alias_name' => 'remove-subtopic-of-topic',
            ])
        ; // Remover o eliminar un subtema de un tema
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'see-questions-of-subtopic',
                'alias_name' => 'see-questions-of-subtopic',
            ])
        ; // Ver las preguntas que tiene un subtema

        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'add-opposition-to-topic',
                'alias_name' => 'add-opposition-to-topic',
            ])
        ; // Agregar una oposicion al tema
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'remove-opposition-of-topic',
                'alias_name' => 'remove-opposition-of-topic',
            ])
        ; // Remover o eliminar una oposicion de un tema

        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'add-question-to-topic',
                'alias_name' => 'add-question-to-topic',
            ])
        ; // Agregar una pregunta al tema
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'see-question-of-topic',
                'alias_name' => 'see-question-of-topic',
            ])
        ; // Visualizar la data de una pregunta que esta asignado a un tema
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'remove-question-of-topic',
                'alias_name' => 'remove-question-of-topic',
            ])
        ; // Remover una pregunta de un tema


        // Permisos: Cuestionarios
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'create-tests-for-resolve',
                'alias_name' => 'create-tests-for-resolve',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'list-uncompleted-tests',
                'alias_name' => 'list-uncompleted-tests',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'resolve-a-tests',
                'alias_name' => 'resolve-a-tests',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'see-results-of-tests',
                'alias_name' => 'see-results-of-tests',
            ]
        );

        // Permisos: Datos de Alumno
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'see-my-information-like-student',
                'alias_name' => 'see-my-information-like-student',
            ]
        );
        Permission::create(
            [
                'id' => UuidGeneratorService::getUUIDUnique(Permission::class),
                'name' => 'see-my-history-results-questions-of-all-tests',
                'alias_name' => 'see-my-history-results-questions-of-all-tests',
            ]
        );

        $role_student = Role::query()->where('name', '=', 'student')
            ->first();
        $role_admin = Role::query()->where('name', '=', 'admin')
            ->first();

        $role_admin->givePermissionTo([
            'list-students',
            'see-a-student',
            'create-student',
            'edit-student',
            'delete-student',
            'action-for-multiple-users',
            'export-students',
            'import-students',
            'enable-account-student',
            'disable-account-student',
            'list-oppositions',
            'create-opposition',
            'edit-opposition',
            'delete-opposition',
            'see-syllabus',
            'add-topic-to-opposition',
            'remove-topic-of-opposition',
            'list-topics',
            'create-topic',
            'edit-topic',
            'delete-topic',
            'import-topics',
            'see-subtopics',
            'see-oppositions',
            'see-questions',
            'add-subtopic-to-topic',
            'edit-subtopic-of-topic',
            'remove-subtopic-of-topic',
            'see-questions-of-subtopic',
            'add-opposition-to-topic',
            'remove-opposition-of-topic',
            'add-question-to-topic',
            'see-question-of-topic',
            'remove-question-of-topic',
        ]);

        $role_student->givePermissionTo([
            'create-tests-for-resolve',
            'list-uncompleted-tests',
            'resolve-a-tests',
            'see-results-of-tests',
            'see-my-information-like-student',
            'see-my-history-results-questions-of-all-tests',
        ]);
    }
}
