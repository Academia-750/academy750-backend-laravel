<?php

namespace App\Console\Commands;

use App\Console\Traits\helpersJsonApi;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeDBApiRestResourceCommand extends Command
{
    use helpersJsonApi;

    protected $signature = 'make:json-api:db-app {name}';

    protected $description = "Crea el 'DB App' del Resource api Rest Core (Encargado de las transacciones y administracion de la informacion de la Base de Datos)";

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
        $path = $this->getSourceFilePath();

        $this->makeDirectory(dirname($path));

        $contents = $this->getSourceFile();

        if( ! $this->filesystem->exists($path) ){
            $this->filesystem->put($path, $contents);

            $this->info("DB App api Rest: {$path} created successfully!");
        }else{
            $this->error("File : {$path} already exits");
        }

    }


    /**
     **
     * Map the stub variables present in stub to its value
     *
     * @return array
     *
     */
    public function getStubVariables():array{
        $pascalCaseNameResourcePlural = $this->getPluralClassName(Str::camel($this->argument('name')));

        return [
            "namespace" => 'App\\Core\\Resources\\'.$pascalCaseNameResourcePlural.'\\v1',
            "namespacedModel" => 'App\\Models\\' . $this->getSingularClassName(Str::camel($this->argument('name'))), // App\Models\User
            "class" => 'DBApp',
            "interfaceName" => $pascalCaseNameResourcePlural. 'Interface', // UsersInterface
            "modelName" => $this->getSingularClassName(Str::camel($this->argument('name'))), // User
            "modelNamePlural" => $this->getPluralClassName(Str::camel($this->argument('name'))), // Users
            "modelVariable" => Str::snake($this->getSingularClassName(Str::camel($this->argument('name')))), // process_time
            "modelVariablePlural" => Str::snake($this->getPluralClassName(Str::camel($this->argument('name')))), // process_times
            "namespacedInterface" => 'App\\Core\\Resources\\'.$pascalCaseNameResourcePlural.'\\v1\\Interfaces\\' . $pascalCaseNameResourcePlural . 'Interface', // App\Core\Resources\Users\Interfaces\UsersInterface
        ];
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath():string{
        return base_path("stubs\\academia750\\core\\db.api-rest.stub");
    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath():string{

        $resourceName = $this->getPluralClassName(Str::camel($this->argument('name')));
        return base_path("app/Core/Resources/".$resourceName. '/v1/DBApp.php');
    }
}
