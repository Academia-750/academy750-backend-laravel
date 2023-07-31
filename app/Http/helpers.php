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
function parseFilter($key, $value, $operation = '=', $opts = [])
{
    if (is_null($value)) {
        return null;
    }
    return function ($query) use ($value, $operation, $key, $opts) {

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
                        $value = is_array($value) ? $value : [$value];
                        array_map(fn($item) => $subQuery->orWhere($key, 'like', '%' . $item . '%'), $value);
                    }, $key);
                });
                break;
            case 'notHave':
                if (!$opts['field']) {
                    throw new \Exception('Error: Parse filter `notHave` operation requires of the $opts["fields"]');
                }
                $query->whereDoesntHave($key, function ($query) use ($value, $opts) {

                    $query->where($opts['field'], $value);
                });
                break;
            default:
                $query->where($key, $operation, $value);
        };
    };
}


/**
 * Pass all the filters into the query
 */
function filterToQuery($query, $filter)
{
    $filter = removeNull($filter);
    $query->where(function ($query) use ($filter) {
        foreach ($filter as $condition) {
            $condition($query);
        }
    });

    return $query;
}