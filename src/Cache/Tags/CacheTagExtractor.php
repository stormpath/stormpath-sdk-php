<?php

namespace Stormpath\Cache\Tags;

/**
* 
*/
class CacheTagExtractor
{
    public static function extractCacheTags($document)
    {
        $cacheTags = [];

        if (isset($document->href)) {
            $cacheTags[] = $document->href;
        }

        foreach ($document as $key => $value) {
            if (is_object($value) && isset($value->href)) {
                $cacheTags = array_merge(self::extractCacheTags($value), $cacheTags);
            }

            if ($key === 'items') {
                foreach ($value as $item) {
                    $cacheTags = array_merge(self::extractCacheTags($item), $cacheTags);
                }
            }
        }

        return $cacheTags;
    }
}
