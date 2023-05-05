<?php

namespace App\Core\Services;

trait StateAvailableTrait {

    public function isAvailable(): bool
    {
        if (!$this?->exists) {
            throw new \Exception('No es válido acceder a la propiedad isAvailable de un registro que no existe');
        }

        return $this?->is_available === 'yes';
    }
}
