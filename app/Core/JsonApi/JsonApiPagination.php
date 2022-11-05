<?php

namespace App\Core\JsonApi;

use Illuminate\Database\Eloquent\Builder;

class JsonApiPagination
{
    /**
     * Class JsonApiPagination
     *
     * @package App
     * @mixin Builder
     */

    public function jsonPaginate()
    {
        return function () {

            if(request('page') !== null){
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

            if( request('page') === null && request('page.size') === null && request('page.number') === null){
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
