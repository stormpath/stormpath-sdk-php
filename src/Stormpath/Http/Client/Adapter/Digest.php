<?php

namespace Stormpath\Http\Client\Adapter;

use Stormpath\Service\StormpathService as Stormpath;
use Zend\Http\Client\Adapter\Socket;
use Zend\Http\Request;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;

class Digest extends Socket
{
    /**
     * Send request to the remote server
     *
     * @param string        $method
     * @param \Zend\Uri\Uri $uri
     * @param string        $httpVer
     * @param array         $headers
     * @param string        $body
     * @return string Request as text
     */
    public function write($method, $uri, $httpVer = '1.1', $headers = array(), $body = '')
    {
        $date = new \DateTime();
        $timeStamp = $date->format('Ymd\THms\Z');
        $dateStamp = $date->format('Ymd');
        $nonce = Uuid::uuid4();

        // SAuthc1 requires that we sign the Host header so we
        // have to have it in the request by the time we sign.
        $parsedUrl = parse_url($uri);
        $hostHeader = $parsedUrl['host'];  # Verify host has port #

        $headers['Host'] = $hostHeader;
        $headers['X-Stormpath-Date'] = $timeStamp;

        if ($resourcePath = $parsedUrl['path']) {
            $resourcePath = urlencode($resourcePath);
            $resourcePath = strtr(
                strtr(
                    strtr($resourcePath,
                        ['+' => '%20']
                    ),
                    ['*' =>'%2A']
                ),
                ['%7E' => '~']
            );
            $resourcePath = strtr($resourcePath, array('%2F' => '/'));
        } else {
            $resourcePath = '/';
        }

        $canonicalResourcePath = $resourcePath;
        $canonicalQueryString = (isset($parsedUrl['query'])) ? $parsedUrl['query']: '';
        foreach ($headers as $key => $value) {
            $canonicalHeaders[strtolower($key)] = $value;
        }

        $canonicalHeaderString = implode("\n", $canonicalHeaders);
        $signedHeadersString = implode(';', array_keys($canonicalHeaders));
        $requestPayloadHashHex = $this->toHex($this->hashText($body));

        $canonicalRequest = $method . "\n" .
                            $canonicalResourcePath . "\n" .
                            $canonicalQueryString . "\n" .
                            $canonicalHeaderString . "\n" .
                            $signedHeadersString . "\n" .
                            $requestPayloadHashHex;

        $id = Stormpath::getId() . '/' . $dateStamp . '/' . $nonce . '/sauthc1_request';

        $canonicalRequestHashHex = $this->toHex($this->hashText($canonicalRequest));

        $stringToSign = "HMAC-SHA-256\n" .
                        $timeStamp . "\n" .
                        $id . "\n" .
                        $canonicalRequestHashHex;

        // SAuthc1 uses a series of derived keys, formed by hashing different pieces of data
        $kSecret = $this->toUTF8('SAuthc1' . Stormpath::getSecret());
        $kDate = $this->sign($dateStamp, $kSecret, 'SHA256');
        $kNonce = $this->sign($nonce, $kDate, 'SHA256');
        $kSigning = $this->sign('sauthc1_request', $kNonce, 'SHA256');

        $signature = $this->sign($this->toUTF8($stringToSign), $kSigning, 'SHA256');
        $signatureHex = $this->toHex($signature);

        $authorizationHeader = 'SAuthc1 ' .
                               $this->createNameValuePair('sauthc1Id', $id) . ', ' .
                               $this->createNameValuePair('sauthc1SignedHeaders', $signedHeadersString) . ', ' .
                               $this->createNameValuePair('sauthc1Signature', $signatureHex);

        $headers['Authorization'] = $authorizationHeader;

        $return = parent::write($method, $uri, $httpVer, $headers, $body);

#        print_r($return);die();

        return $return;
    }

/*
    const DEFAULT_ENCODING = 'UTF-8';
    const DEFAULT_ALGORITHM = 'SHA256';
    const HOST_HEADER = 'Host';
    const AUTHORIZATION_HEADER = 'Authorization';
    const STORMPATH_DATE_HEADER = 'X-Stormpath-Date';
    const ID_TERMINATOR = 'sauthc1_request';
    const ALGORITHM = 'HMAC-SHA-256';
    const AUTHENTICATION_SCHEME = 'SAuthc1';
    const SAUTHC1_ID = 'sauthc1Id';
    const SAUTHC1_SIGNED_HEADERS = 'sauthc1SignedHeaders';
    const SAUTHC1_SIGNATURE = 'sauthc1Signature';
    const DATE_FORMAT = 'Ymd';
    const TIMESTAMP_FORMAT = 'Ymd\THms\Z';

    const NL = "\n";

    public function signRequest(Request $request)
    {

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
        $requestPayloadHashHex = $this->toHex($this->hashText($body));

        $canonicalRequest = $method . "\n" .
                            $canonicalResourcePath . "\n" .
                            $canonicalQueryString . "\n" .
                            $canonicalHeaderString . "\n" .
                            $signedHeadersString . "\n" .
                            $requestPayloadHashHex;

        $id = $apiKey->getId() . '/' . $dateStamp . '/' . $nonce . '/' . self::ID_TERMINATOR;

        $canonicalRequestHashHex = $this->toHex($this->hashText($canonicalRequest));

        $stringToSign = self::ALGORITHM . "\n" .
                        $timeStamp . "\n" .
                        $id . "\n" .
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
*/
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
        return hash('SHA256', $this->toUTF8($text), true);
    }

    protected function sign($data, $key, $algorithm)
    {
        $utf8Data = $this->toUTF8($data);

        return hash_hmac($algorithm, $utf8Data, $key, true);
    }

    protected function toUTF8($str)
    {
        return mb_convert_encoding($str, 'UTF-8');
    }

    private function createNameValuePair($name, $value)
    {
        return $name . '=' .$value;
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

            $result .= "\n";
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
