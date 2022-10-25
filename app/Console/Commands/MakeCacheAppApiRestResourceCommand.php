<?php

namespace App\Console\Commands;

use App\Console\Traits\helpersJsonApi;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeCacheAppApiRestResourceCommand extends Command
{
    use helpersJsonApi;

    protected $signature = 'make:json-api:cache-app {name}';

    protected $description = "Crea el 'Cache App' del Resource api Rest Core (Para cachear la informacion de la DB)";

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

            $this->info("Cache App api Rest: {$path} created successfully!");
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
            "namespace" => 'App\\Core\\Resources\\'.$pascalCaseNameResourcePlural,
            "namespacedModel" => 'App\\Models\\' . $this->getSingularClassName(Str::camel($this->argument('name'))), // App\Models\ProcessTime
            "namespacedDB" => '\\App\\Core\\Resources\\'.$pascalCaseNameResourcePlural . '\\DBApp', // App\Core\Resources\ProcessTimes\DBApp
            "class" => 'CacheApp',
            "interfaceName" => $pascalCaseNameResourcePlural . 'Interface', // ProcessTimesInterface
            "dbName" => 'DBApp',
            "dbNameVariable" => 'dbApp',
            "modelVariable" => Str::snake($this->getSingularClassName(Str::camel($this->argument('name')))), // process_time
            "modelVariableTagCache" => Str::snake($this->getSingularClassName(Str::camel($this->argument('name')))), // user
            "namespacedInterface" => 'App\\Core\\Resources\\'. $pascalCaseNameResourcePlural .'\\Interfaces\\' . $pascalCaseNameResourcePlural . 'Interface', // App\Core\Resources\Users\Interfaces\UsersInterface
        ];
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath():string{
        return base_path("stubs\\SymbioticWorld\\core\\cache.api-rest.stub");
    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath():string{
        $resourceName = $this->getPluralClassName(Str::camel($this->argument('name')));

        return base_path("app/Core/Resources/". $resourceName . '/v1/CacheApp.php');
    }
}
