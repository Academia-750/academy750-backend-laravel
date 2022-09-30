<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Permisos: Gestion de Alumnos
        Permission::create(['name' => 'list-students-system']);
        Permission::create(['name' => 'create-student']);
        Permission::create(['name' => 'edit-student']);
        Permission::create(['name' => 'delete-student']);
        Permission::create(['name' => 'ban-student']); // Dar de baja
        Permission::create(['name' => 'unban-student']); // reactivar alumno al sistema

        // Permisos: Gestion de Oposiciones
        Permission::create(['name' => 'list-oppositions']);
        Permission::create(['name' => 'create-opposition']);
        Permission::create(['name' => 'edit-opposition']);
        Permission::create(['name' => 'delete-opposition']);
        Permission::create(['name' => 'see-syllabus']); // ver temario
        Permission::create(['name' => 'add-topic-to-opposition']); // Agregar tema a la oposicion
        Permission::create(['name' => 'remove-topic-of-opposition']); // Remover o eliminar un tema de la oposicion

        // Permisos: Gestion de temas
        Permission::create(['name' => 'list-topics']);
        Permission::create(['name' => 'create-topic']);
        Permission::create(['name' => 'edit-topic']);
        Permission::create(['name' => 'delete-topic']);
        Permission::create(['name' => 'import-topics']);
        Permission::create(['name' => 'see-subtopics']); // ver subtemas
        Permission::create(['name' => 'see-oppositions']); // ver oposiciones
        Permission::create(['name' => 'see-questions']); // ver preguntas

        Permission::create(['name' => 'add-subtopic-to-topic']); // Agregar un subtema al tema
        Permission::create(['name' => 'edit-subtopic-of-topic']); // Editar subtema del tema
        Permission::create(['name' => 'remove-subtopic-of-topic']); // Remover o eliminar un subtema de un tema
        Permission::create(['name' => 'see-questions-of-subtopic']); // Ver las preguntas que tiene un subtema

        Permission::create(['name' => 'add-opposition-to-topic']); // Agregar una oposicion al tema
        Permission::create(['name' => 'remove-opposition-of-topic']); // Remover o eliminar una oposicion de un tema

        Permission::create(['name' => 'add-question-to-topic']); // Agregar una pregunta al tema
        Permission::create(['name' => 'see-question-of-topic']); // Visualizar la data de una pregunta que esta asignado a un tema
        Permission::create(['name' => 'remove-question-of-topic']); // Remover una pregunta de un tema


        // Permisos: Cuestionarios
        Permission::create(['name' => 'create-tests-for-resolve']);
        Permission::create(['name' => 'list-uncompleted-tests']);
        Permission::create(['name' => 'resolve-a-tests']);
        Permission::create(['name' => 'see-results-of-tests']);

        // Permisos: Datos de Alumno
        Permission::create(['name' => 'see-my-information-like-student']);
        Permission::create(['name' => 'see-my-history-results-questions-of-all-tests']);

        $role_admin = Role::query()->where('name', '=', 'admin')->first();
        $role_student = Role::query()->where('name', '=', 'student')->first();

        $role_admin->givePermissionTo([
            'list-students-system',
            'create-student',
            'edit-student',
            'delete-student',
            'ban-student',
            'unban-student',
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
