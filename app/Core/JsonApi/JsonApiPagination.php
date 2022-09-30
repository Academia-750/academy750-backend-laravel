<?php

namespace App\Core\JsonApi;

class JsonApiPagination
{
    public function jsonPaginate()
    {
        return function () {

            if(! is_null( request('page') )){
                abort_unless(
                    is_array(request('page')),
                    400, __('exceptions.pagination.bad_request_invalid_parameter_{page}_must_be_an_array')
                );

                abort_unless(
                    array_key_exists('number',request('page')) && array_key_exists('size',request('page')),
                    400, __('exceptions.pagination.bad_request_invalid_parameter_without_{page[size]}&page[number]}'));

                abort_unless(
                    is_numeric( request('page.size') ) && is_numeric( request('page.number') ) ,
                    400, __('exceptions.pagination.bad_request_invalid_parameters_{page[size]}&{page[number]}must_be_numeric_values'));


            }

            if( is_null(request('page')) && is_null(request('page.size')) && is_null(request('page.number')) ){
                return $this->get();
            }

            return $this->paginate(
                $perPage = request('page.size'),
                $columns = ['*'],
                $pageName = 'page[number]',
                $page = request('page.number')
            )->appends( request()->except('page.number') );
        };
    }
}
