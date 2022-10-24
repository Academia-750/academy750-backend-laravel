<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Pluralizer;
use App\Console\Traits\helpersJsonApi;
use Illuminate\Support\Str;

class MakeResourceJsonApiCoreCommand extends Command
{
    use helpersJsonApi;

    protected $signature = 'make:json-api:resource {name}';

    protected $description = 'Crea todas las capas del Recurso Solicitado (ResourceApiController, Interface, Authorizer, Cache, Event, SchemaJson y DB ) y demas archivos';

    public function __construct()
    {

        parent::__construct();

    }

    public function handle()
    {
        $argName = Str::camel($this->argument('name'));
        $argNameSingular = $this->getSingularClassName($argName);
        $argNamePlural = $this->getPluralClassName($argName);

        //Create Controller
        Artisan::call('make:json-api:controller', ['name' => $argNamePlural]);
        $this->info("Controller created successfully!");

        //Create Events Realtime
        Artisan::call('make:json-api:events-resource', ['name' => $argNamePlural]);
        $this->info("Controller created successfully!");

        //Create Interface
        Artisan::call('make:json-api:interface', ['name' => $argName]);
        $this->info("Interface created successfully!");

        //Create Authorizer layer
        Artisan::call('make:json-api:authorizer', ['name' => $argName]);
        $this->info("Authorizer created successfully!");

        //Create Schema Json layer
        Artisan::call('make:json-api:schema-json', ['name' => $argName]);
        $this->info("Schema Json created successfully!");

        //Create Event layer
        Artisan::call('make:json-api:event-app', ['name' => $argName]);
        $this->info("Event App created successfully!");

        //Create Cache layer
        Artisan::call('make:json-api:cache-app', ['name' => $argName]);
        $this->info("Cache App created successfully!");

        //Create DBApp layer
        Artisan::call('make:json-api:db-app', ['name' => $argName]);
        $this->info("DB App created successfully!");

        //Create Policy
        Artisan::call('make:policy',['name' => 'Api/'.$argNameSingular.'Policy','--model' => $argNameSingular]);
        $this->info("Policy created successfully!");

        //Create Model and Migration
        Artisan::call('make:model',['name' => $argNameSingular,'-m'=> true]);
        $this->info("Model created successfully.");
        $this->info("Migration created successfully.");

        //Crear el Seeder
        Artisan::call('make:seeder', ['name' => "{$argNamePlural}Seeder"]);
        $this->info("Seeder created successfully!");

        //Hace el registro automatico del Seeder en el DatatableSeeder.php
        Artisan::call('register:json-api:seeder', ['name' => $argNamePlural]);
        $this->info("Seeder register successfully!");

        //Create Factory
        //make:factory ZoneFactory --model Zone
        Artisan::call('make:factory',['name' => $argNamePlural.'Factory','--model'=> $argNameSingular]);
        $this->info("Factory created successfully.");

        //Create Resources
        Artisan::call('make:resource',['name' => 'Api/'.$argNamePlural.'/'.$argNameSingular.'Resource']);
        Artisan::call('make:resource',['name' => 'Api/'.$argNamePlural.'/'.$argNameSingular.'Collection']);
        $this->info("Resource and Collection created successfully.");

        //Create Requests
        Artisan::call('make:request',['name' => 'Api/'.$argNamePlural.'/Create'.$argNameSingular.'Request']);
        Artisan::call('make:request',['name' => 'Api/'.$argNamePlural.'/Update'.$argNameSingular.'Request']);
        /*Artisan::call('make:request',['name' => 'Api/'.$argNamePlural.'/Export'.$argNamePlural.'Request']);*/
        Artisan::call('make:request',['name' => 'Api/'.$argNamePlural.'/ActionForMassiveSelection'.$argNamePlural.'Request']);
        Artisan::call('make:request',['name' => 'Api/'.$argNamePlural.'/Import'.$argNamePlural.'Request']);
        $this->info("Requests created successfully.");

        //Crear el archivo blade para exportacion PDF
        /*Artisan::call('make:json-api:export-pdf-view', ['name' => $argName]);
        $this->info("File Export View Blade created successfully!");*/

        //Crear el archivo de rutas
        Artisan::call('make:json-api:routes', ['name' => $argName]);
        $this->info("File Routes created successfully!");

        // Crear el archivo de los canales realtime
        /*Artisan::call('make:json-api:channels', ['name' => $argName]);
        $this->info("File Channels Realtime created successfully!");*/

        //Create Export and Import files Laravel Excel
        /*Artisan::call('make:json-api:import', ['name' => $argName]);
        Artisan::call('make:json-api:export', ['name' => $argName]);
        $this->info("Export and Import files - Laravel Excel");*/

        //Create Test
        Artisan::call('make:test', ['name' => $argNamePlural.'/create'.$argNamePlural.'/'.$argNamePlural.'CreateTest']);
        Artisan::call('make:test', ['name' => $argNamePlural.'/create'.$argNamePlural.'/'.$argNamePlural.'CreateValidationTest']);
        Artisan::call('make:test', ['name' => $argNamePlural.'/delete'.$argNamePlural.'/Delete'.$argNamePlural.'Test']);
        Artisan::call('make:test', ['name' => $argNamePlural.'/get'.$argNamePlural.'/Filter'.$argNamePlural.'Test']);
        Artisan::call('make:test', ['name' => $argNamePlural.'/get'.$argNamePlural.'/IncludeRelationships'.$argNamePlural.'Test']);
        Artisan::call('make:test', ['name' => $argNamePlural.'/get'.$argNamePlural.'/List'.$argNamePlural.'Test']);
        Artisan::call('make:test', ['name' => $argNamePlural.'/get'.$argNamePlural.'/Paginate'.$argNamePlural.'Test']);
        Artisan::call('make:test', ['name' => $argNamePlural.'/get'.$argNamePlural.'/Sort'.$argNamePlural.'Test']);
        Artisan::call('make:test', ['name' => $argNamePlural.'/update'.$argNamePlural.'/'.$argNamePlural.'UpdateTest']);
        Artisan::call('make:test', ['name' => $argNamePlural.'/update'.$argNamePlural.'/'.$argNamePlural.'UpdateValidationTest']);
        $this->info("Tests created successfully.");
    }
}
