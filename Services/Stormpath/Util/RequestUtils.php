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

    /**
     * Returns {@code true} if the specified parsed url (array result of a call to parse_url(url))
     * uses a standard port (i.e. http == 80 or https == 443),
     * {@code false} otherwise.
     *
     * @param $parsedUrl
     * @return true if the specified parsed url is using a non-standard port, false otherwise
     */
    public static function isDefaultPort(array $parsedUrl)
    {
        $scheme = strtolower($parsedUrl['scheme']);
        $port = array_key_exists('port', $parsedUrl) ? $parsedUrl['port'] : $scheme == 'https' ? 443 : 0;
        return $port <= 0 or ($port == 80 and $scheme == 'http') or ($port == 443 and $scheme == 'https');
    }

    public static function encodeUrl($value, $path, $canonical)
    {
        if (!$value)
        {
            return '';
        }

        $encoded = urlencode($value);

        if ($canonical)
        {
            $encoded = strtr(
                           strtr(
                               strtr($encoded,
                                   array('+' => '%20')),
                                   array('*' =>'%2A')),
                                   array('%7E' => '~'));

            if ($path)
            {
                $encoded = strtr($encoded, array('%2F' => '/'));
            }
        }

        return $encoded;
    }
}
