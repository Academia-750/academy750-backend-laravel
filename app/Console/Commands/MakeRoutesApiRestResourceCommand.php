<?php

namespace App\Console\Commands;

use App\Console\Traits\helpersJsonApi;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class MakeRoutesApiRestResourceCommand extends Command
{
    use helpersJsonApi;

    protected $signature = 'make:json-api:routes {name}';

    protected $description = "Crea el archivo que contendrá las rutas de tipo Api esenciales para el recurso del Resource api Rest Core";

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

            $this->info("Routes api Rest: {$path} created successfully!");

            Artisan::call('register:json-api:routes', ['name' => $this->argument('name')]);
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
        $nameRouteResource = Str::snake(
            Str::camel(
                $this->getPluralClassName($this->argument('name'))
            ),
            '-'
        );

        $argumentRoute = Str::snake(
            Str::camel(
                $this->getSingularClassName($this->argument('name'))
            )
        );

        return [
            "nameController" => $this->getPluralClassName(Str::camel($this->argument('name'))),
            "prefixRoute" => $nameRouteResource,
            "nameRoute" => $nameRouteResource,
            "argumentRouteSingular" => $argumentRoute
        ];
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath():string{
        return base_path("stubs\\academia750\\routes\\routes.api-rest.stub");
    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath():string{

        $resourceNameFormatter = Str::snake(
            Str::camel(
                $this->getPluralClassName($this->argument('name'))
            ),
            '-'
        );

        return base_path("routes/api/v1/routes/". $resourceNameFormatter . '.routes.php');
    }
}
