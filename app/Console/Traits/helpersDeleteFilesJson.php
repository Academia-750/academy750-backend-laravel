<?php

namespace App\Console\Traits;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

trait helpersDeleteFilesJson
{
    use helpersJsonApi;

    protected function delete_register_interface_resource ($argumentName): void {
        $filesystemInstance = new Filesystem;
        $interfaceProviderPath = base_path("app/Providers/InstanceInterfaceAppProvider.php");
        $NewContent = "// [EndOfLineMethodRegister]";
        $contentFile = file_get_contents($interfaceProviderPath);
        $resourceName = $this->getPluralClassName(Str::camel(
            $argumentName
        ));
        $searchContentForReplace = "app()->bind(\App\Core\Resources\\{$resourceName}\Interfaces\\{$resourceName}Interface::class, \App\Core\Resources\\{$resourceName}\Authorizer::class);
        // [EndOfLineMethodRegister]";
        $contentPutFile = str_replace($searchContentForReplace , $NewContent, $contentFile);

        $filesystemInstance->replace($interfaceProviderPath, $contentPutFile);
    }

    protected function delete_register_routes_api_rest_resource ($argumentName): void {
        $filesystemInstance = new Filesystem;
        $registerRoutesIndexPath = base_path("routes/api/v1/index.php");
        $NewContent = "// [EndOfLineMethodRegister]";
        $contentFile = file_get_contents($registerRoutesIndexPath);
        $resourceName = Str::snake(
            Str::camel(
                $this->getPluralClassName($argumentName)
            ),
            '-'
        );
        $searchContentForReplace = "require __DIR__ . '/routes/{$resourceName}.routes.php';
    // [EndOfLineMethodRegister]";
        $contentPutFile = str_replace($searchContentForReplace , $NewContent, $contentFile);

        $filesystemInstance->replace($registerRoutesIndexPath, $contentPutFile);
    }

    protected function delete_register_channels_realtime_api_resource ($argumentName): void {
        $filesystemInstance = new Filesystem;
        $registerChannelsRealtimeIndexPath = base_path("routes/channels.php");
        $NewContent = "// [EndOfLineMethodRegister]";
        $contentFile = file_get_contents($registerChannelsRealtimeIndexPath);
        $resourceName = Str::snake(
            Str::camel(
                $this->getPluralClassName($argumentName)
            ),
            '-'
        );
        $searchContentForReplace = "require __DIR__ . '/channels/{$resourceName}.channels.php';
    // [EndOfLineMethodRegister]";
        $contentPutFile = str_replace($searchContentForReplace , $NewContent, $contentFile);

        $filesystemInstance->replace($registerChannelsRealtimeIndexPath, $contentPutFile);
    }

    protected function delete_register_seeders_api_resource ($argumentName): void {
        $filesystemInstance = new Filesystem;
        $registerDatabaseSeedersPath = base_path("database/seeders/DatabaseSeeder.php");
        $NewContent = "// [EndOfLineMethodRegister]";
        $contentFile = file_get_contents($registerDatabaseSeedersPath);
        $resourceName = $this->getPluralClassName(Str::camel($argumentName));
        $searchContentForReplace = "\$this->call({$resourceName}Seeder::class);
        // [EndOfLineMethodRegister]";
        $contentPutFile = str_replace($searchContentForReplace , $NewContent, $contentFile);

        $filesystemInstance->replace($registerDatabaseSeedersPath, $contentPutFile);
    }
}
