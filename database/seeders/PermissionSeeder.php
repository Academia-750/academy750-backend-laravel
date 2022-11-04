<?php

namespace Database\Seeders;

use App\Core\Services\UserServiceTrait;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    use UserServiceTrait;

    public function run(): void
    {
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'list-students',
                'alias_name' => 'list-students',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'see-a-student',
                'alias_name' => 'see-a-student',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'create-student',
                'alias_name' => 'create-student',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'edit-student',
                'alias_name' => 'edit-student',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'delete-student',
                'alias_name' => 'delete-student',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'export-students',
                'alias_name' => 'export-students',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'import-students',
                'alias_name' => 'import-students',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'enable-account-student',
                'alias_name' => 'enable-account-student',
            ])
        ; // Dar de baja
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'disable-account-student',
                'alias_name' => 'disable-account-student',
            ])
        ; // reactivar alumno al sistema

        // Permisos: Gestion de Oposiciones
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'list-oppositions',
                'alias_name' => 'list-oppositions',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'create-opposition',
                'alias_name' => 'create-opposition',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'edit-opposition',
                'alias_name' => 'edit-opposition',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'delete-opposition',
                'alias_name' => 'delete-opposition',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'see-syllabus',
                'alias_name' => 'see-syllabus',
            ])
        ; // ver temario
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'add-topic-to-opposition',
                'alias_name' => 'add-topic-to-opposition',
            ])
        ; // Agregar tema a la oposicion
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'remove-topic-of-opposition',
                'alias_name' => 'remove-topic-of-opposition',
            ])
        ; // Remover o eliminar un tema de la oposicion

        // Permisos: Gestion de temas
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'list-topics',
                'alias_name' => 'list-topics',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'create-topic',
                'alias_name' => 'create-topic',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'edit-topic',
                'alias_name' => 'edit-topic',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'delete-topic',
                'alias_name' => 'delete-topic',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'import-topics',
                'alias_name' => 'import-topics',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'see-subtopics',
                'alias_name' => 'see-subtopics',
            ])
        ; // ver subtemas
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'see-oppositions',
                'alias_name' => 'see-oppositions',
            ])
        ; // ver oposiciones
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'see-questions',
                'alias_name' => 'see-questions',
            ])
        ; // ver preguntas

        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'add-subtopic-to-topic',
                'alias_name' => 'add-subtopic-to-topic',
            ])
        ; // Agregar un subtema al tema
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'edit-subtopic-of-topic',
                'alias_name' => 'edit-subtopic-of-topic',
            ])
        ; // Editar subtema del tema
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'remove-subtopic-of-topic',
                'alias_name' => 'remove-subtopic-of-topic',
            ])
        ; // Remover o eliminar un subtema de un tema
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'see-questions-of-subtopic',
                'alias_name' => 'see-questions-of-subtopic',
            ])
        ; // Ver las preguntas que tiene un subtema

        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'add-opposition-to-topic',
                'alias_name' => 'add-opposition-to-topic',
            ])
        ; // Agregar una oposicion al tema
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'remove-opposition-of-topic',
                'alias_name' => 'remove-opposition-of-topic',
            ])
        ; // Remover o eliminar una oposicion de un tema

        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'add-question-to-topic',
                'alias_name' => 'add-question-to-topic',
            ])
        ; // Agregar una pregunta al tema
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'see-question-of-topic',
                'alias_name' => 'see-question-of-topic',
            ])
        ; // Visualizar la data de una pregunta que esta asignado a un tema
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'remove-question-of-topic',
                'alias_name' => 'remove-question-of-topic',
            ])
        ; // Remover una pregunta de un tema


        // Permisos: Cuestionarios
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'create-tests-for-resolve',
                'alias_name' => 'create-tests-for-resolve',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'list-uncompleted-tests',
                'alias_name' => 'list-uncompleted-tests',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'resolve-a-tests',
                'alias_name' => 'resolve-a-tests',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'see-results-of-tests',
                'alias_name' => 'see-results-of-tests',
            ]
        );

        // Permisos: Datos de Alumno
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
                'name' => 'see-my-information-like-student',
                'alias_name' => 'see-my-information-like-student',
            ]
        );
        Permission::create(
            [
                'id' => $this->getUUIDUnique(),
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
            'create-student',
            'edit-student',
            'delete-student',
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
