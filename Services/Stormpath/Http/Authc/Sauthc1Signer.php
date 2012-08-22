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
    const DEFAULT_ENCODING       = 'UTF-8';
    const DEFAULT_ALGORITHM      = 'SHA256';
    const HOST_HEADER            = 'Host';
    const AUTHORIZATION_HEADER   = 'Authorization';
    const STORMPATH_DATE_HEADER  = 'X-Stormpath-Date';
    const ID_TERMINATOR          = 'sauthc1_request';
    const ALGORITHM              = 'HMAC-SHA-256';
    const AUTHENTICATION_SCHEME  = 'SAuthc1';
    const SAUTHC1_ID             = 'sauthc1Id';
    const SAUTHC1_SIGNED_HEADERS = 'sauthc1SignedHeaders';
    const SAUTHC1_SIGNATURE      = 'sauthc1Signature';
    const DATE_FORMAT            = 'Ymd';
    const TIMESTAMP_FORMAT       = 'Ymd\THms\Z';
    const TIME_ZONE              = 'UTC';
    const NL                     = "\n";

    public function signRequest(Services_Stormpath_Http_Request $request, Services_Stormpath_Client_ApiKey $apiKey)
    {
        date_default_timezone_set(self::TIME_ZONE);
        $date = new DateTime();
        $timeStamp = $date->format(self::TIMESTAMP_FORMAT);
        $dateStamp = $date->format(self::DATE_FORMAT);

        $nonce = Services_Stormpath_Util_UUID::generate(Services_Stormpath_Util_UUID::UUID_RANDOM,
                                                        Services_Stormpath_Util_UUID::FMT_STRING);

        $parsedUrl = parse_url($request->getResourceUrl());

        // SAuthc1 requires that we sign the Host header so we
        // have to have it in the request by the time we sign.
        $hostHeader = $parsedUrl['host'];

        if (!Services_Stormpath_Util_RequestUtils::isDefaultPort($parsedUrl))
        {
            $hostHeader .= ':' . $parsedUrl['port'];
        }

        $requestHeaders = $request->getHeaders();

        $requestHeaders[self::HOST_HEADER] = $hostHeader;
        $requestHeaders[self::STORMPATH_DATE_HEADER] = $timeStamp;

        $request->setHeaders($requestHeaders);

        $method = $request->getMethod();
        $canonicalResourcePath = $this->canonicalizeResourcePath($parsedUrl['path']);
        $canonicalQueryString = $this->canonicalizeQueryString($request);
        $canonicalHeaderString = $this->canonicalizeHeaders($request);
        $signedHeadersString = $this->getSignedHeaders($request);
        $requestPayloadHashHex = $this->toHex($this->hashText($this->getRequestPayload($request)));

        $canonicalRequest = $method . self::NL .
                            $canonicalResourcePath . self::NL .
                            $canonicalQueryString . self::NL .
                            $canonicalHeaderString . self::NL .
                            $signedHeadersString . self::NL .
                            $requestPayloadHashHex;

        $id = $apiKey->getId() . '/' . $dateStamp . '/' . $nonce . '/' . self::ID_TERMINATOR;

        $canonicalRequestHashHex = $this->toHex($this->hashText($canonicalRequest));

        $stringToSign = self::ALGORITHM . self::NL .
                        $timeStamp . self::NL .
                        $id . self::NL .
                        $canonicalRequestHashHex;

        // SAuthc1 uses a series of derived keys, formed by hashing different pieces of data
        $kSecret = $this->toUTF8(self::AUTHENTICATION_SCHEME . $apiKey->getSecret());
        $kDate = $this->sign($dateStamp, $kSecret, self::DEFAULT_ALGORITHM);
        $kNonce = $this->sign($nonce, $kDate, self::DEFAULT_ALGORITHM);
        $kSigning = $this->sign(self::ID_TERMINATOR, $kNonce, self::DEFAULT_ALGORITHM);

        $signature = $this->sign($this->toUTF8($stringToSign), $kSigning, self::DEFAULT_ALGORITHM);
        $signatureHex = $this->toHex($signature);

        $authorizationHeader = self::AUTHENTICATION_SCHEME . ' ' .
                               $this->createNameValuePair(self::SAUTHC1_ID, $id) . ', ' .
                               $this->createNameValuePair(self::SAUTHC1_SIGNED_HEADERS, $signedHeadersString) . ', ' .
                               $this->createNameValuePair(self::SAUTHC1_SIGNATURE, $signatureHex);

        $requestHeaders[self::AUTHORIZATION_HEADER] = $authorizationHeader;

        $request->setHeaders($requestHeaders);

    }

    public function toHex($data)
    {
        $result = unpack('H*', $data);
        return $result[1];
    }

    protected function canonicalizeQueryString(Services_Stormpath_Http_Request $request)
    {
       return $request->toStrQueryString(true);
    }

    protected function hashText($text)
    {
        return hash(self::DEFAULT_ALGORITHM, $this->toUTF8($text), true);
    }

    protected function sign($data, $key, $algorithm)
    {
        $utf8Data = $this->toUTF8($data);

        return hash_hmac($algorithm, $utf8Data, $key, true);
    }

    protected function toUTF8($str)
    {
        return mb_convert_encoding($str, self::DEFAULT_ENCODING);
    }

    protected function getRequestPayload(Services_Stormpath_Http_Request $request)
    {
        return $this->getRequestPayloadWithoutQueryParams($request);
    }


    protected function getRequestPayloadWithoutQueryParams(Services_Stormpath_Http_Request $request)
    {
        $result = '';

        if ($request->getBody())
        {
            $result = $request->getBody();
        }

        return $result;
    }

    private function createNameValuePair($name, $value)
    {
        return $name . '=' .$value;
    }

    private function canonicalizeResourcePath($resourcePath)
    {
        if ($resourcePath)
        {
            return Services_Stormpath_Util_RequestUtils::encodeUrl($resourcePath, true, true);
        } else
        {
            return '/';
        }
    }

    private function canonicalizeHeaders(Services_Stormpath_Http_Request $request)
    {
        $requestHeaders = $request->getHeaders();
        ksort($requestHeaders);
        $request->setHeaders($requestHeaders);

        $result = '';

        foreach($request->getHeaders() as $key => $val)
        {
            $result .= strtolower($key) . ':' . $val;

            $result .= self::NL;
        }

        return $result;
    }

    private function getSignedHeaders(Services_Stormpath_Http_Request $request)
    {

        $result = '';

        foreach($request->getHeaders() as $key => $val)
        {
            if ($result)
            {
                $result .= ';' . $key;
            } else
            {
                $result .= $key;
            }
        }

        return strtolower($result);
    }
}
