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
            case 'between':
                $query->where(function ($subQuery) use ($value, $key) {
                    $subQuery
                        ->where($key, '>=', $value['from'])
                        ->where($key, '<', $value['to']);
                });
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



abstract class DocumentType
{
    const PDF = 'PDF';
    const WORD = 'Word Document';
    const IMAGE = 'Image';
    const VIDEO = 'Video';
    const UNKNOWN = 'Unknown Document Type';
    const EMPTY = 'Empty';
}


function getDocumentTypeFromURL($url)
{
    if (!$url) {
        return DocumentType::EMPTY;
    }

    /**
     * We skip this call on TESTING environment where the URLs are fake
     */
    // Get the headers of the remote file using HEAD request
    $headers = config('app.env') !== 'testing' ? get_headers($url, 1) : [];

    // Check if a 'Content-Type' header is present
    if (isset($headers['Content-Type'])) {
        $contentType = $headers['Content-Type'];

        // Check if it's a PDF
        if (strpos($contentType, 'application/pdf') !== false) {
            return DocumentType::PDF;
        }
        // Check if it's a Word document (DOC or DOCX)
        if (strpos($contentType, 'application/msword') !== false || strpos($contentType, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') !== false) {
            return DocumentType::WORD;
        }
        // Check if it's an image (common image types)
        if (strpos($contentType, 'image/') === 0) {
            return DocumentType::IMAGE;
        }
        // Check if it's a video (common video types)
        if (strpos($contentType, 'video/') === 0) {
            return DocumentType::VIDEO;
        }
        // Add more checks for other document types as needed

        // If none of the known content types match, you can return a generic type
        return DocumentType::UNKNOWN;
    }

    // If there's no 'Content-Type' header, you can also check the file extension
    $pathInfo = pathinfo($url);
    $extension = strtolower($pathInfo['extension']);

    // Check the file extension for known types
    switch ($extension) {
        case 'pdf':
            return DocumentType::PDF;
        case 'doc':
        case 'docx':
            return DocumentType::WORD;
        // Check for common image extensions
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
        case 'bmp':
            return DocumentType::IMAGE;
        // Check for common video extensions
        case 'mp4':
        case 'avi':
        case 'mov':
        case 'wmv':
            return DocumentType::VIDEO;
        // Add more cases for other document types
        default:
            return DocumentType::UNKNOWN;
    }
}