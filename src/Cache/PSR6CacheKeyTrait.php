<?php

namespace Stormpath\Cache;

trait PSR6CacheKeyTrait
{
    protected function createCacheKey($href, $query = null, $options = [])
    {
        if (is_array($query)) {
            ksort($query);
            $query = http_build_query($query);
        }
        $key = $href.'?'.$query;
        if(!empty($options)) {
            $key .= '_' . implode('_',$options);
        }
        $key = preg_replace('/[^0-9A-Za-z\_\.]/', '', $key);
        return $key;
    }
}
