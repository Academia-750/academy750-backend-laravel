<?php


/**
 * Remove the null elements of an array
 */
function removeNull($arr)
{
    return array_filter($arr, static function ($var) {
        return $var !== null;
    });
}

/**
 * Same as  ?? operand.
 * @deprecated
 */
function defaultValue($value, $default)
{
    return !is_null($value) ? $value : $default;
}

/**
 * Helper for the get API. Maps a hash to a query object
 */
function parseFilter($key, $value, $operation = '=')
{
    if (is_null($value)) {
        return null;
    }
    return function ($query) use ($value, $operation, $key) {

        switch ($operation) {
            case 'in':
                $query->whereIn($key, $value);
                break;
            case 'isNull':
                $value ? $query->whereNull($key) : $query->whereNotNull($key);
                break;
            case 'or_like':
                $query->where(function ($subQuery) use ($value, $key) {
                    array_map(function ($key) use ($value, $subQuery) {
                        $subQuery->orWhere($key, 'like', '%' . $value . '%');
                    }, $key);
                });
                break;
            default:
                $query->where($key, $operation, $value);
        };
    };
}