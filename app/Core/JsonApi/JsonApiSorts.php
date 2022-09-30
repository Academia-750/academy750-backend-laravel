<?php

namespace App\Core\JsonApi;

use Illuminate\Support\Str;

class JsonApiSorts
{
    public function applySorts(){
        return function (){

            if (is_null(request('sort'))) {
                return $this;
            }

            /*Validar si las propiedades $allowedSorts y $adapterSorts existen*/
            abort_unless(
                property_exists($this->model, 'allowedSorts'),
                500,
                __('exceptions.sorts.missing_propierty_class', ['property' => 'allowedSorts' , 'class' => get_class($this->model)])
            );
            abort_unless(
                property_exists($this->model, 'adapterSorts'),
                500,
                __('exceptions.sorts.missing_propierty_class', ['property' => 'adapterSorts' , 'class' => get_class($this->model)])
            );

            $sortFields = Str::of(request('sort'))->explode(',');

            foreach ($sortFields as $sortField) {
                $collectAllowedSort = collect($this->model->allowedSorts);
                $collectAdapterSort = collect($this->model->adapterSorts);
                $direction = 'asc';
                if (Str::of($sortField)->startsWith('-')) {
                    $direction = 'desc';
                    $sortField = (string) Str::of($sortField)->substr(1); // -username = username
                }

                // Evalua si el parametro existe en el arreglo de parametros permitidos para ordenar
                abort_unless(
                    $collectAllowedSort->contains($sortField),
                    400, __('exceptions.sorts.parameter_is_not_allowed', ['property' => $sortField]))
                ;
                // Una vez verificado que esta permitido, buscamos si no existen adaptaciones en el array de adapters
                if( $collectAdapterSort->has($sortField) ){
                    $sortField = $collectAdapterSort->get($sortField);
                }

                $sortField = 'sort'. $sortField;
                $this->model->{$sortField}($this,$direction);
            }
            return $this;
        };
    }
}
