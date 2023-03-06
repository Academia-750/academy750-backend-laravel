<?php

namespace App\Console\Commands;

use App\Console\Traits\helpersDeleteFilesJson;
use App\Console\Traits\helpersJsonApi;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class DeleteResourceApiRestCommand extends Command
{
    use helpersJsonApi, helpersDeleteFilesJson;

    protected $signature = 'delete:json-api:resource {name}';

    protected $description = "Elimina todos los archivos creados para el recurso que coincidan con el nombre pasado como argumento ";

    public $filesystem;

    /**
     * Create a new command instance.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {

        parent::__construct();

        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     */
    public function handle():void
    {
        foreach ($this->get_paths() as $path) {
            // \Log::debug($path);
            if( $this->filesystem->exists($path) ){
                $this->filesystem->delete($path);
            }
        }

        foreach ($this->get_directories() as $directory) {
            // \Log::debug($directory);
            if( $this->filesystem->exists($directory) ){
                $this->filesystem->deleteDirectory($directory);
            }
        }

        // Eliminamos la migracion
        //$this->delete_migration_file();
        $this->delete_register_files();

    }

    protected function get_paths (): array {

        $resourceNamePascalCasePlural = $this->getPluralClassName(Str::camel($this->argument('name')));
        $resourceNamePascalCaseSingular = $this->getSingularClassName(Str::camel($this->argument('name')));
        $resourceNameSnakeCaseSlug = Str::snake(Str::camel($this->getPluralClassName($this->argument('name'))), '-');
        $resourceNameSnakeCaseUnderline = Str::snake(Str::camel($this->getPluralClassName($this->argument('name'))));
        $nameFormatterPluralSnakeCaseSlug = Str::snake(
            Str::camel(
                $this->getPluralClassName($this->argument('name'))
            ),
            '-'
        );
        return array(
            "app/Http/Controllers/Api/v1/{$resourceNamePascalCasePlural}Controller.php",
            "app/Models/{$resourceNamePascalCaseSingular}.php",
            "app/Policies/Api/v1/{$resourceNamePascalCasePlural}Policy.php",
            "database/factories/{$resourceNamePascalCasePlural}Factory.php",
            "database/seeders/{$resourceNamePascalCasePlural}Seeder.php",
            // Core Files
            /*"app/Core/Resources/v1{$resourceNamePascalCasePlural}/Authorizer.php",
            "app/Core/Resources/v1{$resourceNamePascalCasePlural}/CacheApp.php",
            "app/Core/Resources/v1{$resourceNamePascalCasePlural}/DBApp.php",
            "app/Core/Resources/v1{$resourceNamePascalCasePlural}/EventApp.php",
            "app/Core/Resources/v1{$resourceNamePascalCasePlural}/SchemaJson.php",
            "app/Core/Resources/v1". $resourceNamePascalCasePlural ."/Interfaces/". $resourceNamePascalCasePlural . 'Interface.php',*/
            // Routes
            "routes/api/v1/routes/{$nameFormatterPluralSnakeCaseSlug}.routes.php",
            /*"routes/channels/". $nameFormatterPluralSnakeCaseSlug . '.channels.php',*/
            // Events Realtime Notify
            /*"app/Events/Api/" . $resourceNamePascalCasePlural . '/' . 'ActionForMassiveSelection' . $resourceNamePascalCaseSingular . "Event.php",
            "app/Events/Api/" . $resourceNamePascalCasePlural . '/' . 'Create' . $resourceNamePascalCaseSingular . 'Event.php',
            "app/Events/Api/" . $resourceNamePascalCasePlural . '/' . 'Delete' . $resourceNamePascalCaseSingular . 'Event.php',
            "app/Events/Api/" . $resourceNamePascalCasePlural . '/' . 'Import' . $resourceNamePascalCasePlural . 'Event.php',
            "app/Events/Api/" . $resourceNamePascalCasePlural . '/' . 'Update' . $resourceNamePascalCaseSingular . 'Event.php',*/
            // Export and Import Process Files
            /*"app/Exports/Api/". $resourceNamePascalCasePlural . "/" .$resourceNamePascalCasePlural . "Export.php",
            "app/Imports/Api/" . $resourceNamePascalCasePlural . '/' .$resourceNamePascalCasePlural. 'Import.php',
            "resources/views/resources/pdf/datatables-info/".$resourceNameSnakeCaseSlug. '.blade.php'*/
        );
    }

    protected function get_directories (): array {
        $resourceNamePascalCasePlural = $this->getPluralClassName(Str::camel($this->argument('name')));
        return array(
            "app/Core/Resources/{$resourceNamePascalCasePlural}/v1",
            "app/Events/Api/{$resourceNamePascalCasePlural}",
            "app/Http/Requests/Api/v1/{$resourceNamePascalCasePlural}",
            "app/Http/Resources/Api/{$resourceNamePascalCasePlural}/v1",
            "app/Imports/Api/{$resourceNamePascalCasePlural}/v1",
            "app/Exports/Api/{$resourceNamePascalCasePlural}/v1",
            "tests/Feature/{$resourceNamePascalCasePlural}",
        );
    }

    protected function delete_register_files (): void  {
        // Funcion encargada de quitar el registro de la interfaz, routes y channels del resource
        $this->delete_register_interface_resource($this->argument('name'));
        $this->delete_register_routes_api_rest_resource($this->argument('name'));
        $this->delete_register_channels_realtime_api_resource($this->argument('name'));
        $this->delete_register_seeders_api_resource($this->argument('name'));
    }

    protected function delete_migration_file () {
        $nameMigration = Str::snake(Str::camel($this->getPluralClassName($this->argument('name'))));
        $files_directory = $this->filesystem->files('database/migrations');
        /*\Log::debug(
            serialize($files_directory)
        );*/
        $nameMigrationArrayResult = array_filter($files_directory, function ($path) use ($nameMigration) {
            return (strrpos($path, "create_{$nameMigration}_table")) !== null;
        });

        /*\Log::debug(
            serialize($nameMigrationArrayResult)
        );*/

        if ($this->filesystem->exists($nameMigrationArrayResult[0])) {
            /*\Log::debug(
                "Si existe la migracion"
            );*/
            $this->filesystem->delete($nameMigrationArrayResult[0]);
        }
    }
}
