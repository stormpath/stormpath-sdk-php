<?php

/*
 * Copyright 2012 Stormpath, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

class Services_Stormpath_Util_YAMLUtil
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
