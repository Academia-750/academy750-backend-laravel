<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $factoryInstancia = Factory::create();

        $usuarios = array(
            [
                'name' => 'Adolph',
                'last_name' => 'Feria',
                'number_phone' => '987654321',
                'email' => 'adolfoferia@gmail.com', // is unique
                'password' => bcrypt('12345678'),
                'rol' => "Admin"
            ],
            [
                'name' => 'Abraham',
                'last_name' => 'Flores',
                'number_phone' => '988998988',
                'email' => 'abraham@gmail.com', // is unique
                'password' => bcrypt('12345678'),
                'rol' => "Admin"
            ],
            [
                'name' => 'Raul',
                'last_name' => 'Moheno',
                'number_phone' => '9984875616',
                'email' => 'raul@gmail.com', // is unique
                'password' => bcrypt('bomberos750'),
                'rol' => "Admin"
            ],
            [
                'name' => $factoryInstancia->name(),
                'last_name' => $factoryInstancia->lastName(),
                'number_phone' => $factoryInstancia->phoneNumber(),
                'email' => 'alumno01@gmail.com', // is unique
                'password' => bcrypt('bomberos750'),
                'rol' => "student"
            ],
            [
                'name' => $factoryInstancia->name(),
                'last_name' => $factoryInstancia->lastName(),
                'number_phone' => $factoryInstancia->phoneNumber(),
                'email' => 'alumno02@gmail.com', // is unique
                'password' => bcrypt('bomberos750'),
                'rol' => "student"
            ],
            [
                'name' => $factoryInstancia->name(),
                'last_name' => $factoryInstancia->lastName(),
                'number_phone' => $factoryInstancia->phoneNumber(),
                'email' => 'alumno03@gmail.com', // is unique
                'password' => bcrypt('bomberos750'),
                'rol' => "student"
            ],
            [
                'name' => $factoryInstancia->name(),
                'last_name' => $factoryInstancia->lastName(),
                'number_phone' => $factoryInstancia->phoneNumber(),
                'email' => 'alumno04@gmail.com', // is unique
                'password' => bcrypt('bomberos750'),
                'rol' => "student"
            ],
        );


        foreach ($usuarios as $usuario) {
            $this->registrar_usuario($usuario);
        }
    }

    public function generar_documento_identidad($longitud = 8)
    {
        $documento = '';
        for ($iterator = 1; $iterator <= $longitud; $iterator++) {

            $documento.=random_int(1,9);

        }
        $numeros=intval($documento);
        $letra=substr("TRWAGMYFPDXBNJZSQVHLCKE", $numeros%23, 1);
        $documento.=$letra;
        return $documento;
    }

    public function registrar_usuario($datos_array)
    {
        $usuario_creado = User::query()->create([
            "identification_number" => ($datos_array['name']!='Adolph') ? $this->obtener_documento_unico() : "42711006Y",
            "name" => $datos_array['name'],
            "last_name" => $datos_array["last_name"],
            "number_phone" => $datos_array["number_phone"],
            "email" => $datos_array["email"],
            "password" => $datos_array["password"]
        ]);

        $usuario_creado->assignRole($datos_array['rol']);

        return $usuario_creado;
    }

    public function obtener_documento_unico()
    {
        $documento_identidad = $this->generar_documento_identidad();
        $existe_documento_identidad = User::query()->where('identification_number', '=', $documento_identidad)->count();

        // Siempre que encuentre un documento, va a seguir generando más hasta generar uno que no coincida en la tabla
        while ($existe_documento_identidad !== 0) {
            $documento_identidad = $this->generar_documento_identidad();
        }

        return $documento_identidad;
    }

}
