<?php

namespace Stormpath\Util;

class YAMLUtil
{
    const NOT_NESTED_VALUE_FOUND = 'NOT_NESTED_VALUE_FOUND';

    public static function retrieveNestedValue(array $arrayToSearch, array $keys)
    {
        $obj = (object) $arrayToSearch;

        foreach($keys as $key)
        {
            if ($obj instanceof stdClass)
            {
                if (isset($obj->$key))
                {
                    $obj = $obj->$key;
                }

            } elseif (is_array($obj))
            {
                $obj = (object) $obj;
                $obj = $obj->$key;
            }
        }

        return $obj instanceof stdClass ? self::NOT_NESTED_VALUE_FOUND : $obj;
    }

}
