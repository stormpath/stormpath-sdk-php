<?php
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

namespace Stormpath\Authc\Api;

use JWT;
use Stormpath\Client;

class Request
{
    protected static $request = null;

    protected $headers = null;

    private $apiId;

    private $apiSecret;

    private $scheme;

    private function __construct()
    {
        $this->headers = $_SERVER;
    }

    public static function createFromGlobals()
    {
        if (!isset(self::$request)) {
            self::$request = new self();
        }

        return self::$request->build();
    }

    private function build()
    {
        $tokens = $this->getAuthenticationTokens();
        $this->apiId = $tokens[0];
        $this->apiSecret = $tokens[1];
        $schemeAndValue = $this->getSchemeAndValue();
        $this->scheme = $schemeAndValue[0];

        return $this;
    }


    public function hasAuthorizationHeader()
    {
        return isset($this->headers['HTTP_AUTHORIZATION']);
    }

    public function hasGrantType()
    {
        $validGrantTypes = [
            'client_credentials',
            'password',
            'refresh_token'
        ];
        return !!(strpos($this->headers['REQUEST_URI'], '?grant_type=') && in_array($this->getGrantType(), $validGrantTypes));
    }

    public function isBearerAuthorization()
    {
        $sandv = $this->getSchemeAndValue();
        return !!($sandv[0]=='Bearer');
    }

    public function isBasicAuthorization()
    {
        $sandv = $this->getSchemeAndValue();
        return !!($sandv[0]=='Basic');
    }

    public function isPasswordGrantType()
    {
        if (!$this->hasGrantType()) return false;

        if ($this->getGrantType() != 'password') return false;

        return true;
    }

    private function getGrantType()
    {
        $queryString = $this->headers['QUERY_STRING'];

        $query = explode('=',$queryString,2);

        return $query[1];
    }

    private function getAuthenticationTokens()
    {
        $encodedAuthenticationTokens = $this->getEncodedAuthenticationToken();
        $decoded = base64_decode($encodedAuthenticationTokens);

        $sandv = $this->getSchemeAndValue();

        if($sandv[0] == 'Basic') {
            $tokens = explode(":", $decoded, 2);
        } else if($sandv[0] == 'Bearer') {
            $apiSecret = Client::getInstance()->getDataStore()->getApiKey()->getSecret();
            $token = JWT::decode($encodedAuthenticationTokens, $apiSecret, array('HS256'));
            $tokens = array($token->sub, null);

        } else {
            throw new \InvalidArgumentException('The Scheme is not valid');
        }

        if(count($tokens) != 2)
            throw new \InvalidArgumentException('It appears the Authorization header is not formatted correctly.');

        return $tokens;
    }

    private function getEncodedAuthenticationToken()
    {
        $schemeAndValue = $this->getSchemeAndValue();

        if(count($schemeAndValue) != 2)
            throw new \InvalidArgumentException('It seems your Authorization header is formatted incorrectly. Please
                                                 ensure it is formatted correctly and try again.');

        return $schemeAndValue[1];


    }

    public function getSchemeAndValue()
    {

        if($this->headers === null)
            throw new \InvalidArgumentException('Uh Oh.  Something happened and the headers are not available.
                                                 Please try again and if you continue to have this issue, contact
                                                 support and let them know what you were trying to do.');


        if(!isset($this->headers['HTTP_AUTHORIZATION']))
            throw new \InvalidArgumentException('You need to supply the authorization header as part of your request.
                                                 Please add the header and try again.');

        return explode(" ", $this->headers['HTTP_AUTHORIZATION'], 2);
    }

    public function getApiId()
    {
        return $this->apiId;
    }

    public function getApiSecret()
    {
        return $this->apiSecret;
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public static function tearDown()
    {
        static::$request = NULL;
    }


}