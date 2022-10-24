<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AutoCreateEventsRealtimeResource extends Command
{
    protected $signature = 'make:json-api:events-resource {name}';

    protected $description = 'Command description';

    public function handle(): void
    {
        Artisan::call('make:json-api:event-create', ['name' => $this->argument('name')]);
        $this->info("Event Notify Create Item created successfully!");
        Artisan::call('make:json-api:event-update', ['name' => $this->argument('name')]);
        $this->info("Event Notify Update Item created successfully!");
        Artisan::call('make:json-api:event-delete', ['name' => $this->argument('name')]);
        $this->info("Event Notify Delete Item created successfully!");
        Artisan::call('make:json-api:event-action-massive-selection', ['name' => $this->argument('name')]);
        $this->info("Event Notify Action Massive Selection Item created successfully!");
        Artisan::call('make:json-api:event-import', ['name' => $this->argument('name')]);
        $this->info("Event Notify Process Import Items created successfully!");
    }
}
