<?php

namespace App\Console\Commands;

use App\Console\Traits\helpersJsonApi;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

class MakeEventUpdateItemApiRestResourceCommand extends Command
{

    use helpersJsonApi;

    protected $signature = 'make:json-api:event-update {name}';

    protected $description = "Crea el 'Evento' especifico para notificar la actualizacion de un nuevo recurso";

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

            $this->info("Event Realtime 'Update Item' api Rest: {$path} created successfully!");
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
            "namespace" => 'App\\Events\\Api\\'. $pascalCaseNameResourcePlural,
            "modelVariable" => Str::snake($this->getSingularClassName(Str::camel($this->argument('name')))),
            "class" => 'Update' . $this->getSingularClassName(Str::camel($this->argument('name'))) .'Event',
            "resourceName" => Str::snake($pascalCaseNameResourcePlural, '-'),
            "modelPlural" => $pascalCaseNameResourcePlural //Users
        ];
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath():string{
        return base_path("stubs\\academia750\\events\\event-update.item.api-rest.stub");
    }


    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath():string{
        $resourceNameSingular = $this->getSingularClassName(Str::camel($this->argument('name')));
        $resourceNamePlural = $this->getPluralClassName(Str::camel($this->argument('name')));
        return base_path("app/Events/Api/" . $resourceNamePlural . '/' . 'Update' . $resourceNameSingular . 'Event.php'); //App/Events/Api/ProcessTimeEvent.php
    }

}
