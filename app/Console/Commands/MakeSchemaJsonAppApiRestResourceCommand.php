<?php

namespace App\Console\Commands;

use App\Console\Traits\helpersJsonApi;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeSchemaJsonAppApiRestResourceCommand extends Command
{
    use helpersJsonApi;

    protected $signature = 'make:json-api:schema-json {name}';

    protected $description = "Crea el 'Schema Json' del Resource api Rest Core";

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

            $this->info("Schema Json api Rest: {$path} created successfully!");
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
            "namespace" => 'App\\Core\\Resources\\'.$pascalCaseNameResourcePlural. '\\v1',
            "namespacedModel" => 'App\\Models\\' . $this->getSingularClassName(Str::camel($this->argument('name'))), // App\Models\User
            "namespacedEvent" => '\\App\\Core\\Resources\\'. $pascalCaseNameResourcePlural . '\\v1\\EventApp', // App\Core\Resources\Users\EventApp
            "class" => 'SchemaJson',
            "interfaceName" => $pascalCaseNameResourcePlural. 'Interface', // UsersInterface
            "eventName" => 'EventApp',
            "eventNameVariable" => 'eventApp',
            "modelVariable" => Str::snake($this->getSingularClassName(Str::camel($this->argument('name')))), // user
            "modelVariablePluralForFiles" => $this->getPluralClassName(Str::camel($this->argument('name'))), // Users
            "namespacedInterface" => 'App\\Core\\Resources\\'.$pascalCaseNameResourcePlural.'\\v1\\Interfaces\\' . $pascalCaseNameResourcePlural . 'Interface', // App\Core\Resources\Users\Interfaces\UsersInterface
        ];
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath():string{
        return base_path("stubs\\academia750\\core\\jsonresponse.api-rest.stub");
    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath():string{
        $resourceName = $this->getPluralClassName(Str::camel($this->argument('name')));
        return base_path("app/Core/Resources/".$resourceName. '/v1/SchemaJson.php');
    }
}
