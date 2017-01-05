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

namespace Stormpath\Util;

use Stormpath\DataStore\DataStore;

class NonceStore
{
    // @codeCoverageIgnoreStart
    private $pool;

    public static function generateNonce()
    {
        return UUID::v4();
    }

    public function __construct(DataStore $dataStore)
    {
        $this->pool = $dataStore->getCachePool();
    }

    public function getNonce($nonce)
    {
        $item = $this->pool->getItem('nonce_'.$nonce);

        return $item->get();
    }

    public function putNonce($nonce)
    {
        $item = $this->pool->getItem('nonce_'.$nonce);
        $item->set($nonce);
        $item->expiresAfter(60);
        $this->pool->save($item);
    }
    // @codeCoverageIgnoreStart
}
