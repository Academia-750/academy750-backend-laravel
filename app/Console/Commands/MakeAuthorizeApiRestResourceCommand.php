<?php

namespace App\Console\Commands;

use App\Console\Traits\helpersJsonApi;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeAuthorizeApiRestResourceCommand extends Command
{
    use helpersJsonApi;

    protected $signature = 'make:json-api:authorizer {name}';

    protected $description = "Crea el 'Authorizer' del Resource api Rest Core";

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

            $this->info("Authorizer api Rest: {$path} created successfully!");
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
            "namespacedModel" => 'App\\Models\\' . $this->getSingularClassName(Str::camel($this->argument('name'))), // App\Models\User
            "namespacedSchema" => '\\App\\Core\\Resources\\'.$pascalCaseNameResourcePlural . '\\SchemaJson', // App\Core\Resources\Users\SchemaJson
            "class" => 'Authorizer', //class Authorizer
            "interfaceName" => $pascalCaseNameResourcePlural. 'Interface', // UsersInterface
            "schemaName" => 'SchemaJson',
            "schemaNameVariable" => 'schemaJson',
            "modelVariable" => Str::snake($this->getSingularClassName(Str::camel($this->argument('name')))), // process_time
            "namespacedInterface" => 'App\\Core\\Resources\\'.$pascalCaseNameResourcePlural.'\\Interfaces\\' . $pascalCaseNameResourcePlural . 'Interface', // App\Core\Resources\Users\Interfaces\UsersInterface
            "modelInstanceClass" => $this->getSingularClassName(Str::camel($this->argument('name'))) . '::class', // ProcessTime::class
        ];
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath():string{
        return base_path("stubs\\SymbioticWorld\\core\\access.authorizer.api-rest.stub");
    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath():string{
        $resourceName = $this->getPluralClassName(Str::camel($this->argument('name')));
        return base_path("app/Core/Resources/". $resourceName . '/v1/Authorizer.php');
    }
}
