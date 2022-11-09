<?php

namespace App\Console\Commands;

use App\Console\Traits\helpersJsonApi;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeEventAppApiRestResourceCommand extends Command
{
    use helpersJsonApi;

    protected $signature = 'make:json-api:event-app {name}';

    protected $description = "Crea el 'Event App' del Resource api Rest Core (Para disparar eventos o trabajos)";

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

            $this->info("Event App api Rest: {$path} created successfully!");
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
        $prefixNamespacedEvents = "App\\Events\\Api\\";
        return [
            "namespace" => 'App\\Core\\Resources\\'.$pascalCaseNameResourcePlural . '\\v1',
            "namespacedModel" => 'App\\Models\\' . $this->getSingularClassName(Str::camel($this->argument('name'))), // App\Models\User
            "namespacedEventNotifyCreatedItem" => $prefixNamespacedEvents . $pascalCaseNameResourcePlural . '\\v1\\' . 'Create' . $this->getSingularClassName(Str::camel($this->argument('name'))) . 'Event',
            "namespacedEventNotifyUpdateItem" => $prefixNamespacedEvents . $pascalCaseNameResourcePlural . '\\v1\\' . 'Update' . $this->getSingularClassName(Str::camel($this->argument('name'))) . 'Event',
            "namespacedEventNotifyDeleteOrCancelItem" => $prefixNamespacedEvents . $pascalCaseNameResourcePlural . '\\v1\\' . 'Delete' . $this->getSingularClassName(Str::camel($this->argument('name'))) . 'Event',
            "namespacedEventNotifyActionForMassiveSelectionItem" => $prefixNamespacedEvents . $pascalCaseNameResourcePlural . '\\v1\\' . 'ActionForMassiveSelection' . $this->getSingularClassName(Str::camel($this->argument('name'))) . 'Event',
            "namespacedCache" => 'App\\Core\\Resources\\'.$pascalCaseNameResourcePlural . '\\v1\\CacheApp', // App\Core\Resources\Users\EventApp
            'resourceNameSingular' => $this->getSingularClassName(Str::camel($this->argument('name'))),
            'resourceNamePluralSnakeCase' => Str::snake($this->getPluralClassName(Str::camel($this->argument('name')))),
            "class" => 'EventApp',
            "interfaceName" => $pascalCaseNameResourcePlural. 'Interface', // UsersInterface
            "cacheName" => 'CacheApp',
            "cacheNameVariable" => 'cacheApp',
            "modelVariable" => Str::snake($this->getSingularClassName(Str::camel($this->argument('name')))), // user
            "namespacedInterface" => 'App\\Core\\Resources\\'.$pascalCaseNameResourcePlural.'\\v1\\Interfaces\\' . $pascalCaseNameResourcePlural . 'Interface', // App\Core\Resources\Users\Interfaces\UsersInterface
        ];
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath():string{
        return base_path("stubs\\academia750\\core\\events.api-rest.stub");
    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath():string{
        $resourceName = $this->getPluralClassName(Str::camel($this->argument('name')));
        return base_path("app/Core/Resources/".$resourceName. '/v1/EventApp.php');
    }
}
