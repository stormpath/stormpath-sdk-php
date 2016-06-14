<?php namespace Stormpath\Cache;
/*
 * Copyright 2016 Stormpath, Inc.
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

abstract class Cacheable {

    protected function resourceIsCacheable($resource)
    {
        $cache = true;

        // Check to see if it is a collection
        // All collections will have an items array in the data
        // If it is a collection, we do not want to cache it;
        if(isset($resource->items)) $cache = false;

        // We dont want to cache if it does not have a href.
        if(!isset($resource->href)) $cache = false;

        // Do Not Cache something with ONLY 1 item (href only typically)
        if(count((array)$resource) == 1) $cache = false;

        return $cache;

    }

}
