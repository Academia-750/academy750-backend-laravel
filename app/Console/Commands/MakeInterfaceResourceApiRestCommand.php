<?php

namespace App\Console\Commands;

use App\Console\Traits\helpersJsonApi;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

class MakeInterfaceResourceApiRestCommand extends Command
{
    use helpersJsonApi;

    protected $signature = 'make:json-api:interface {name}';

    protected $description = "Crea el 'Interface' del Resource api Rest Core";

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

            $this->info("Interface api Rest: {$path} created successfully!");

            Artisan::call('register:json-api:interface-core', [
                'name' => $this->argument('name')
            ]);
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
        return [
            "namespace" => 'App\\Core\\Resources\\'.$this->getPluralClassName(Str::camel($this->argument('name'))).'\\v1\\Interfaces',
            "interface" => $this->getPluralClassName(Str::camel($this->argument('name'))) . 'Interface', // UsersInterface
            "modelVariable" => Str::snake($this->getSingularClassName(Str::camel($this->argument('name')))), // process_time
            "namespacedModel" => 'App\\Models\\' . $this->getSingularClassName(Str::camel($this->argument('name'))), // App\Models\User
            "modelName" => $this->getSingularClassName(Str::camel($this->argument('name'))), // User
        ];
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath():string{
        return base_path("stubs\\academia750\\core\\interface.api-rest.stub");
    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath():string{

        $resourceNameFormatter = $this->getPluralClassName(Str::camel($this->argument('name')));
        return base_path("app/Core/Resources/". $resourceNameFormatter ."/v1/Interfaces/". $resourceNameFormatter . 'Interface.php');
    }
}
