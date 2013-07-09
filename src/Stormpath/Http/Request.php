<?php
/*
 * Copyright 2013 Stormpath, Inc.
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

namespace Stormpath\Http;

use Stormpath\Http\HttpMessage;

interface Request extends HttpMessage {


    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_DELETE = 'DELETE';

    public function getMethod();

    public function getResourceUrl();

    public function getQueryString();

    public function setQueryString(array $queryString);

    public function setBody($body, $length);

    function toStrQueryString($canonical);

    function setResourceUrl($resourceUrl);
}
