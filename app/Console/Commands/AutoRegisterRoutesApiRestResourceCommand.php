<?php

namespace App\Console\Commands;

use App\Console\Traits\helpersJsonApi;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class AutoRegisterRoutesApiRestResourceCommand extends Command
{
    use helpersJsonApi;

    protected $signature = 'register:json-api:routes {name}';

    protected $description = "Hace el registro de las nuevas rutas del nuevo recurso creado con cierto nombre";

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
            $this->info("New routes was register successfully!");
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
        $nameResource = Str::snake(
            Str::camel(
                $this->getPluralClassName($this->argument('name'))
            ),
            '-'
        );
        return "require __DIR__ . '/routes/{$nameResource}.routes.php';
    // [EndOfLineMethodRegister]";
    }


    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath():string{
        return base_path("routes/api/v1/index.php");
    }
}
