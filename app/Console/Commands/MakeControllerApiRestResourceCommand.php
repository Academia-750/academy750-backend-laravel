<?php

namespace App\Console\Commands;

use App\Console\Traits\helpersJsonApi;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

class MakeControllerApiRestResourceCommand extends Command
{

    use helpersJsonApi;

    protected $signature = 'make:json-api:controller {name}';

    protected $description = "Crea el 'Controller' del Resource api Rest Core";

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
        $path = $this->getSourceFilePath(); //"App\Http\Controllers\api\UsersController"

        $this->makeDirectory(dirname($path));

        $contents = $this->getSourceFile();

        if( ! $this->filesystem->exists($path) ){
            $this->filesystem->put($path, $contents);

            $this->info("Controller api Rest: {$path} created successfully!");
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
            "namespace" => 'App\\Http\\Controllers\\Api\\v1',
            "namespacedModel" => 'App\\Models\\' . $this->getSingularClassName(Str::camel($this->argument('name'))), // App\Models\User
            "namespacedInterface" => 'App\\Core\\Resources\\'.$pascalCaseNameResourcePlural.'\\v1\\Interfaces\\' . $pascalCaseNameResourcePlural . 'Interface', // App\Core\Resources\Users\Interfaces\UsersInterface
            "class" => $pascalCaseNameResourcePlural . 'Controller', //class UsersController
            "interfaceName" => $pascalCaseNameResourcePlural. 'Interface', // UsersInterface
            "interfaceNameVariable" => Str::camel($this->getPluralClassName(Str::camel($this->argument('name')))) .'Interface', // usersInterface
            "model" => $this->getSingularClassName(Str::camel($this->argument('name'))), // User
            "modelVariable" => Str::snake($this->getSingularClassName(Str::camel($this->argument('name')))), // user
            "modelPlural" => $pascalCaseNameResourcePlural //Users
        ];
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath():string{
        return base_path("stubs\\academia750\\core\\controller.api-rest.stub");
    }


    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath():string{
        return base_path("app/Http/Controllers/Api/v1"). $this->getPluralClassName(Str::camel($this->argument('name'))) . 'Controller.php'; //
    }

}
