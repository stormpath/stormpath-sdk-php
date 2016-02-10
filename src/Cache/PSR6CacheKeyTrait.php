<?php

namespace Stormpath\Cache;

trait PSR6CacheKeyTrait
{
    protected function createCacheKey($href, $options = [])
    {
        $key = $href;
        if(!empty($options)) {
            $key .= '_' . implode('_',$options);
        }
        $key = preg_replace('/[^0-9A-Za-z\_\.]/', '', $key);
        return $key;
    }
}
