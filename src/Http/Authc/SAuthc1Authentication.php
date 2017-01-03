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

namespace Stormpath\Http\Authc;

use Http\Message\Authentication;
use Psr\Http\Message\RequestInterface;
use Stormpath\ApiKey;
use Stormpath\Stormpath;
use Stormpath\Util\RequestUtils;
use Stormpath\Util\UUID;

class SAuthc1Authentication implements Authentication
{
    const DEFAULT_ENCODING = 'UTF-8';
    const DEFAULT_ALGORITHM = 'SHA256';
    const HOST_HEADER = 'Host';
    const AUTHORIZATION_HEADER = 'Authorization';
    const STORMPATH_DATE_HEADER = 'X-Stormpath-Date';
    const ID_TERMINATOR = 'sauthc1_request';
    const ALGORITHM = 'HMAC-SHA-256';
    const AUTHENTICATION_SCHEME = Stormpath::SAUTHC1_AUTHENTICATION_SCHEME;
    const SAUTHC1_ID = 'sauthc1Id';
    const SAUTHC1_SIGNED_HEADERS = 'sauthc1SignedHeaders';
    const SAUTHC1_SIGNATURE = 'sauthc1Signature';
    const DATE_FORMAT = 'Ymd';
    const TIMESTAMP_FORMAT = 'Ymd\THms\Z';
    const TIME_ZONE = 'UTC';
    const NL = "\n";

    private $apiKey;

    public function __construct(ApiKey $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function authenticate(RequestInterface $request)
    {
        $date = new \DateTime();
	    $date->setTimezone(new \DateTimeZone(self::TIME_ZONE));
        $timeStamp = $date->format(self::TIMESTAMP_FORMAT);
        $dateStamp = $date->format(self::DATE_FORMAT);

        $nonce = UUID::generate(UUID::UUID_RANDOM, UUID::FMT_STRING);

        $uri = $request->getUri();

        // SAuthc1 requires that we sign the Host header so we
        // have to have it in the request by the time we sign.
        $hostHeader = $uri->getHost();

        if (!RequestUtils::isDefaultPort($uri)) {
            $hostHeader .= ':' . $uri->getPort();
        }

        $request = $request
            ->withoutHeader(self::AUTHORIZATION_HEADER)
            ->withHeader(self::HOST_HEADER, $hostHeader)
            ->withHeader(self::STORMPATH_DATE_HEADER, $timeStamp)
        ;

        $method = $request->getMethod();
        $canonicalResourcePath = $this->canonicalizeResourcePath($uri->getPath());
        $canonicalQueryString = $this->canonicalizeQueryString($uri->getQuery());
        $canonicalHeaderString = $this->canonicalizeHeaders($request);
        $signedHeadersString = $this->getSignedHeaders($request);
        $requestPayloadHashHex = $this->toHex($this->hashText($this->getRequestPayload($request)));

        $canonicalRequest = $method . self::NL .
        $canonicalResourcePath . self::NL .
        $canonicalQueryString . self::NL .
        $canonicalHeaderString . self::NL .
        $signedHeadersString . self::NL .
            $requestPayloadHashHex;

        $id = $this->apiKey->getId() . '/' . $dateStamp . '/' . $nonce . '/' . self::ID_TERMINATOR;

        $canonicalRequestHashHex = $this->toHex($this->hashText($canonicalRequest));

        $stringToSign = self::ALGORITHM . self::NL .
        $timeStamp . self::NL .
        $id . self::NL .
            $canonicalRequestHashHex;

        // SAuthc1 uses a series of derived keys, formed by hashing different pieces of data
        $kSecret = $this->toUTF8(self::AUTHENTICATION_SCHEME . $this->apiKey->getSecret());
        $kDate = $this->internalSign($dateStamp, $kSecret, self::DEFAULT_ALGORITHM);
        $kNonce = $this->internalSign($nonce, $kDate, self::DEFAULT_ALGORITHM);
        $kSigning = $this->internalSign(self::ID_TERMINATOR, $kNonce, self::DEFAULT_ALGORITHM);

        $signature = $this->internalSign($this->toUTF8($stringToSign), $kSigning, self::DEFAULT_ALGORITHM);
        $signatureHex = $this->toHex($signature);

        $authorizationHeader = self::AUTHENTICATION_SCHEME . ' ' .
        $this->createNameValuePair(self::SAUTHC1_ID, $id) . ', ' .
        $this->createNameValuePair(self::SAUTHC1_SIGNED_HEADERS, $signedHeadersString) . ', ' .
        $this->createNameValuePair(self::SAUTHC1_SIGNATURE, $signatureHex);

        return $request
            ->withHeader(self::AUTHORIZATION_HEADER, $authorizationHeader)
        ;
    }

    public function toHex($data)
    {
        $result = unpack('H*', $data);
        return $result[1];
    }

    protected function cleanQueryParameters(RequestInterface $request)
    {
        $uri = $request->getUri();
        $queryString = $uri->getQuery();

        if ($queryString === "") {
            return $request;
        }

        $params = explode('&', $queryString);
        $newParams = [];
        foreach ($params as $param) {
            $explodedPair = explode('=', $param);
            $newParams[] = urlencode(trim(urldecode($explodedPair[0]))) . '=' . urlencode(trim(urldecode($explodedPair[1])));
        }
        sort($newParams);
        $newQueryString = implode('&', $newParams);
        $newUri = $uri->withQuery($newQueryString);

        return $request->withUri($newUri);
    }

    protected function hashText($text)
    {
        return hash(self::DEFAULT_ALGORITHM, $this->toUTF8($text), true);
    }

    protected function internalSign($data, $key, $algorithm)
    {
        $utf8Data = $this->toUTF8($data);

        return hash_hmac($algorithm, $utf8Data, $key, true);
    }

    protected function toUTF8($str)
    {
        return mb_convert_encoding($str, self::DEFAULT_ENCODING);
    }

    protected function getRequestPayload(RequestInterface $request)
    {
        return $this->getRequestPayloadWithoutQueryParams($request);
    }

    protected function getRequestPayloadWithoutQueryParams(RequestInterface $request)
    {
        $result = '';

        if ($request->getBody()) {
            $result = (string) $request->getBody();
        }

        return $result;
    }

    private function createNameValuePair($name, $value)
    {
        return $name . '=' . $value;
    }

    private function canonicalizeResourcePath($resourcePath)
    {
        if ($resourcePath) {
            return RequestUtils::encodeUrl($resourcePath, true, true);
        } else {
            return '/';
        }
    }

    public function canonicalizeQueryString($queryString)
    {
        if ($queryString === '') {
            return '';
        }

        $canonicalPairs = [];

        $kvs = explode('&', $queryString);
        foreach ($kvs as $kv) {
            $pair = explode('=', $kv, 2);
            $key = urldecode($pair[0]);
            $value = isset($pair[1]) ? urldecode($pair[1]) : '';

            $canonicalPairs[] = RequestUtils::encodeUrl($key, false, true) . '=' . RequestUtils::encodeUrl($value, false, true);
        }

        sort($canonicalPairs);

        return implode('&', $canonicalPairs);
    }

    private function canonicalizeHeaders(RequestInterface $request)
    {
        $requestHeaders = $request->getHeaders();
        ksort($requestHeaders);

        $result = '';

        foreach ($requestHeaders as $key => $val) {
            foreach ($val as $value) {
                $result .= strtolower($key) . ':' . $value;
            }

            $result .= self::NL;
        }

        return $result;
    }

    private function getSignedHeaders(RequestInterface $request)
    {
        $requestHeaders = $request->getHeaders();
        ksort($requestHeaders);

        $result = '';

        foreach ($requestHeaders as $key => $val) {
            if ($result) {
                $result .= ';' . $key;
            } else {
                $result .= $key;
            }
        }

        return strtolower($result);
    }
}
