<?php

/*
 * Copyright 2012 Stormpath, Inc.
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

class Services_Stormpath_Http_Authc_Sauthc1Signer
{
    const DEFAULT_ALGORITHM = "SHA256";
    const HOST_HEADER = "Host";
    const AUTHORIZATION_HEADER = "Authorization";
    const STORMPATH_DATE_HEADER = "X-Stormpath-Date";
    const ID_TERMINATOR = "sauthc1_request";
    const ALGORITHM = "HMAC-SHA-256";
    const AUTHENTICATION_SCHEME = "SAuthc1";
    const SAUTHC1_ID = "sauthc1Id";
    const SAUTHC1_SIGNED_HEADERS = "sauthc1SignedHeaders";
    const SAUTHC1_SIGNATURE = "sauthc1Signature";
    const DATE_FORMAT = 'Ymd';
    const TIMESTAMP_FORMAT = 'Ymd\THms\Z';
    const TIME_ZONE = "UTC";
    const NL = "\n";

    public function sign(Services_Stormpath_Http_Request $request, Services_Stormpath_Client_ApiKey $apiKey)
    {

    }
}
