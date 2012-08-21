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

class Services_Stormpath_Util_RequestUtils
{

    public static function isDefaultPort(array $parsedUrl)
    {
        $scheme = $parsedUrl['scheme'];
        $port = $parsedUrl['port'];
        return $port <= 0 or ($port == 80 and $scheme == 'http') or ($port == 443 and $scheme == 'https');
    }

    public static function encodeUrl($value, $path, $canonical)
    {
        //TODO: implement
    }
}
