<?php

namespace App\Console\Commands;

use App\Console\Traits\helpersJsonApi;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeViewPDFExportApiRestResourceCommand extends Command
{
    use helpersJsonApi;

    protected $signature = 'make:json-api:export-pdf-view {name}';

    protected $description = "Crea el 'Pdf View Blade' para exportacion de datos del Resource api Rest Core";

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

            $this->info("PDF View Blade api Rest: {$path} created successfully!");
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
            "nameResourcePlural" => $this->getPluralClassName(Str::camel($this->argument('name'))),
            "nameResourcePluralLower" => Str::snake($this->getPluralClassName(Str::camel($this->argument('name')))),
            "nameResourceSingularLower" => Str::snake($this->getSingularClassName(Str::camel($this->argument('name')))),
        ];
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath():string{
        return base_path("stubs\\academia750\\exports-imports\\pdf-view-export.api-rest.stub");
    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath():string{
        $resourceName = Str::snake($this->getPluralClassName(Str::camel($this->argument('name'))), '-');

        return base_path("resources/views/resources/export/templates/pdf/".$resourceName. '.blade.php');
    }
}
