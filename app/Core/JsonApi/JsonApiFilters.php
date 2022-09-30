<?php

namespace App\Core\JsonApi;

class JsonApiFilters
{
    public function applyFilters(){
        return function(){

            abort_unless(
                property_exists($this->model, 'allowedFilters'),
                500,
                __('exceptions.filters.missing_propierty_class', ['property' => 'allowedFilters' , 'class' => get_class($this->model)])
            );
            abort_unless(
                property_exists($this->model, 'adapterFilters'),
                500,
                __('exceptions.filters.missing_propierty_class', ['property' => 'adapterFilters' , 'class' => get_class($this->model)])
            );

            if(is_null(request('filter'))){
                return $this;
            }

            foreach(request('filter') as $filterField => $value){
                $collectAllowedFilters = collect($this->model->allowedFilters);
                $collectAdapterFilters = collect($this->model->adapterFilters);

                abort_unless(
                    $collectAllowedFilters->contains($filterField),
                    400,
                    __('exceptions.filters.parameter_is_not_allowed', ['property' => $filterField])
                );

                // Una vez verificado que esta permitido, buscamos si no existen adaptaciones en el array de adapters
                if( $collectAdapterFilters->has($filterField) ){
                    $filterField = $collectAdapterFilters->get($filterField);
                }

                $filterField = 'filter'. $filterField;

                $this->model->{$filterField}($this,$value);
            }

            return $this;
        };
    }
}
