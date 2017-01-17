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
 *
 */

namespace Stormpath\Cache;

class CacheItemWrapper {

	protected $itemToCache;
	protected $cachedItemMeta = [];

	public function __construct($itemToCache) {
		$this->itemToCache = $itemToCache;
	}

	public function setCachedItem($itemToCache) {
		$this->itemToCache = $itemToCache;
	}

	public function getCachedItem() {
		return $this->itemToCache;
	}

	public function addMetaItem($key, $value) {
		$this->cachedItemMeta[$key] = $value;
	}

	public function getMetaItem($key) {
		if(! key_exists($key, $this->cachedItemMeta)) {
			return null;
		}

		return $this->cachedItemMeta[$key];
	}

}
