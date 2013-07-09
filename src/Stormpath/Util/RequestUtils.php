<?php

namespace Stormpath\Util;

class RequestUtils
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
