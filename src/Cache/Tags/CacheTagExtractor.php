<?php

namespace Stormpath\Cache\Tags;

/**
* 
*/
class CacheTagExtractor
{
    public static function extractCacheTags($document, $expandString)
    {
        $cacheTags = [];

        preg_match_all('/([^\,\(]+)(\(.+?\))?,?/', $expandString, $matches);

        foreach ($matches[1] as $expansion) {
            if (isset($document->$expansion)) {
                $cacheTags = array_merge(self::processExpansion($document->$expansion), $cacheTags);
            }
        }

        return $cacheTags;
    }

    private static function processExpansion($expansion)
    {
        $cacheTags = [];
        if(isset($expansion->href)) {
            $cacheTags[] = $expansion->href;
        }

        if (isset($expansion->items)) {
            $cacheTags = array_merge(self::processCollectionItems($expansion->items), $cacheTags);
        }

        return $cacheTags;
    }

    private static function processCollectionItems($items)
    {
        $cacheTags = [];
        foreach ($items as $item) {
            if (isset($item->href)) {
                $cacheTags[] = $item->href;
            }
        }
        return $cacheTags;
    }
}
