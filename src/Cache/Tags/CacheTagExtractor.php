<?php
/**
 * Copyright 2017 Stormpath, Inc.
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

namespace Stormpath\Cache\Tags;

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
