<?php

namespace App\Console\Commands;

use App\Console\Traits\helpersJsonApi;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class AutoRegisterInterfaceControllerExecutableRealtimeApiRestResourceCommand extends Command
{
    use helpersJsonApi;

    protected $signature = 'register:json-api:interface-core {name}';

    protected $description = "Hace el registro del archivo que se ejecutará cuando se hace la instancia de la interfaz del nuevo recurso creado con cierto nombre";

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

        if( $this->filesystem->exists($path) ){
            $contents = $this->getNewSourceRegisterLines();
            $this->filesystem->replace($path, $contents);
            $this->info("New register interface executable was register successfully!");
        }

    }


    /**
     **
     * Replace string of the stub for register like new lines
     *
     * @return string
     *
     */
    public function getStubNewLines():string{
        $resource = $this->getPluralClassName(Str::camel($this->argument('name')));
        return "app()->bind(\App\Core\Resources\\{$resource}\\v1\\Interfaces\\{$resource}Interface::class, \App\Core\Resources\\{$resource}\Authorizer::class);
        // [EndOfLineMethodRegister]";
    }


    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath():string{
        return base_path("app/Providers/InstanceInterfaceAppProvider.php");
    }
}
