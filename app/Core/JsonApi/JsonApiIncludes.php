<?php

namespace App\Core\JsonApi;

use Illuminate\Support\Str;

class JsonApiIncludes
{
    public function applyIncludes(){
        return function () {

            // Verifico si en la peticion se realiza algun include
            if (is_null($includeRequest = request('include'))) {
                return $this;
            }

            // Verifico que el modelo tenga la propiedad allowedIncludes
            abort_unless(
                property_exists($this->model, 'allowedIncludes'),
                500,
                __('exceptions.includes.missing_propierty_class', ['property' => 'allowedIncludes' , 'class' => get_class($this->model)])
            );
            abort_unless(
                property_exists($this->model, 'adapterIncludes'),
                500,
                __('exceptions.includes.missing_propierty_class', ['property' => 'adapterIncludes' , 'class' => get_class($this->model)])
            );

            // Separar include por comas en array de includeList
            $includeList = Str::of($includeRequest)->explode(',');

            // Interamos cada relacion solicitada
            foreach ($includeList as $relationName) {
                $collectAllowedIncludes = collect($this->model->allowedIncludes);
                $collectAdapterIncludes = collect($this->model->adapterIncludes);

                // Verificamos si en la propiedad allowedIncludes contiene la relacion solicitada
                abort_unless(
                    $collectAllowedIncludes->contains($relationName),
                    400,
                    __('exceptions.includes.parameter_is_not_allowed', ['relation' => $relationName])
                );


                /*abort_unless(
                    method_exists($this->model, $relationName),
                    500,
                    __('exceptions.includes.missing_method_class', ['method' => $relationName , 'class' => get_class($this->model)])
                );*/

                // Una vez verificado que esta permitido, buscamos si no existen adaptaciones en el array de adapters
                if( $collectAdapterIncludes->has($relationName) ){
                    $relationName = $collectAdapterIncludes->get($relationName);
                }

                // Realizamos la carga de la relación. Puedes invocar por ejemplo la relacion con roles
                // Si quieres acceder a la relacion de permisos a traves de los roles, tu relacion debe ser así: roles.permissions
                $this->with($relationName);

            }
            return $this;
        };
    }
}
