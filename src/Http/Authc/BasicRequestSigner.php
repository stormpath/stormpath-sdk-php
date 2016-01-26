<?php

namespace Stormpath\Http\Authc;

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

use Stormpath\ApiKey;
use Stormpath\Http\Request;
use Stormpath\Stormpath;

class BasicRequestSigner implements RequestSigner
{
    const AUTHORIZATION_HEADER   = 'Authorization';
    const STORMPATH_DATE_HEADER  = 'X-Stormpath-Date';
    const AUTHENTICATION_SCHEME  = Stormpath::BASIC_AUTHENTICATION_SCHEME;

    const TIMESTAMP_FORMAT       = 'Ymd\THms\Z';
    const TIME_ZONE              = 'UTC';

    const NL                     = "\n";

    public function sign(Request $request, ApiKey $apiKey)
    {
        date_default_timezone_set(self::TIME_ZONE);
        $date = new \DateTime();
        $timeStamp = $date->format(self::TIMESTAMP_FORMAT);

        $requestHeaders = $request->getHeaders();

        unset($requestHeaders[self::STORMPATH_DATE_HEADER]);
        unset($requestHeaders[self::AUTHORIZATION_HEADER]);

        $authorizationHeader = base64_encode($apiKey->getId() . ":" . $apiKey->getSecret());

        $requestHeaders[self::STORMPATH_DATE_HEADER] = $timeStamp;
        $requestHeaders[self::AUTHORIZATION_HEADER] = self::AUTHENTICATION_SCHEME . " " . $authorizationHeader;

        $request->setHeaders($requestHeaders);
    }
}