<?php

namespace Stormpath\Http\Client\Adapter;

use Stormpath\Service\StormpathService as Stormpath;
use Stormpath\Client\ApiKey;
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
        echo "this is the parsedurl: ";
        print_r(implode(',',$parsedUrl));

        print_r("this is the path: " . $parsedUrl['path']);
 
        //$hostHeader = $parsedUrl['host'];  # Verify host has port #

        //$headers['Host'] = $hostHeader;
        $headers['X-Stormpath-Date'] = $timeStamp;
        $headers['Accept'] = 'application/json';
        $headers['User-Agent'] = 'StormpathClient-PHP';

        if ($resourcePath = $parsedUrl['path']) {
            $encoded = urlencode($resourcePath);
            $resourcePath = strtr(
                strtr(
                    strtr($encoded,
                        array('+' => '%20')
                    ),
                    array('*' =>'%2A')
                ),
                array('%7E' => '~')
            );
            $resourcePath = strtr($resourcePath, array('%2F' => '/'));
        } 
        else {
            $resourcePath = '/';
        }

        $canonicalResourcePath = $resourcePath;
        $canonicalQueryString = (isset($parsedUrl['query'])) ? $parsedUrl['query']: '';
        print_r("This is the canonical resource path: " . $canonicalResourcePath);

        foreach ($headers as $key => $value) {
            $canonicalHeaders[strtolower($key)] = $value;
        }

        ksort($canonicalHeaders);
        $headers = $canonicalHeaders;

        $canonicalHeaderString = '';
        foreach ($headers as $key => $val) {
            $canonicalHeaderString .= "$key:$val\n";
        }

        print_r("this is the ksorted headers string: ". $canonicalHeaderString);

//        $canonicalHeaderString = implode("\n", $canonicalHeaders);
        $signedHeadersString = implode(';', array_keys($headers));

        print_r("this is the signed header String: " . $signedHeadersString);

        $requestPayloadHashHex = $this->toHex($this->hashText($body));

        print_r("this is the request payloadhex: ". $requestPayloadHashHex);

        $canonicalRequest = $method . "\n" .
                            $canonicalResourcePath . "\n" .
                            $canonicalQueryString . "\n" .
                            $canonicalHeaderString . "\n" .
                            $signedHeadersString . "\n" .
                            $requestPayloadHashHex;

        print_r("this is the canonical request: " . $canonicalRequest);


        $id = Stormpath::getApiKey()->getId() . '/' . $dateStamp . '/' . $nonce . '/sauthc1_request';

        $canonicalRequestHashHex = $this->toHex($this->hashText($canonicalRequest));

        print_r("this the canonical Request hex: " . $canonicalRequestHashHex);


        $stringToSign = "HMAC-SHA-256\n" .
                        $timeStamp . "\n" .
                        $id . "\n" .
                        $canonicalRequestHashHex;

        print_r("String to sign: " . $stringToSign);

        // SAuthc1 uses a series of derived keys, formed by hashing different pieces of data
        $kSecret = $this->toUTF8('SAuthc1' . Stormpath::getApiKey()->getSecret());
        print_r("ksecret: ". $kSecret);
        $kDate = $this->sign($dateStamp, $kSecret, 'SHA256');
        print_r("kDate: ".  $kDate );
        $kNonce = $this->sign($nonce, $kDate, 'SHA256');
        print_r("kNonce: " . $kNonce);
        $kSigning = $this->sign('sauthc1_request', $kNonce, 'SHA256');
        print_r("ksigning: " . $kSigning);
        $signature = $this->sign($this->toUTF8($stringToSign), $kSigning, 'SHA256');
        print_r("signature: " . $signature );
        $signatureHex = $this->toHex($signature);
        print_r("signatureHex: " . $signatureHex);

        $authorizationHeader = 'SAuthc1 ' .
                               $this->createNameValuePair('sauthc1Id', $id) . ', ' .
                               $this->createNameValuePair('sauthc1SignedHeaders', $signedHeadersString) . ', ' .
                               $this->createNameValuePair('sauthc1Signature', $signatureHex);

        print_r("authorizationHeader: " . $authorizationHeader);

        $headers['authorization'] = $authorizationHeader;

        $return = parent::write($method, $uri, $httpVer, $headers, $body);
        
        print_r("Return Value: " . $return);

        return $return;
    }

    public function toHex($data)
    {
        $result = unpack('H*', $data);
        return $result[1];
    }

    protected function hashText($text)
    {
        return hash('SHA256', $this->toUTF8($text), true);
    }

    protected function sign($data, $key, $algorithm)
    {
//        $utf8Data = $this->toUTF8($data);

        return hash_hmac($algorithm, $data, $key, true);
    }

    protected function toUTF8($str)
    {
        return mb_convert_encoding($str, 'UTF-8');
    }

    private function createNameValuePair($name, $value)
    {
        return $name . '=' .$value;
    }

}
