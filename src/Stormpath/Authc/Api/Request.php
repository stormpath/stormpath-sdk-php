<?php

namespace Stormpath\Authc\Api;


class Request
{
    protected static $request = null;

    protected $headers = null;

    private $apiId;

    private $apiSecret;

    private $scheme;

    private function __construct()
    {
        if(function_exists('getallheaders')) {
            $this->headers = getallheaders();
        } else {
            $this->headers = $_REQUEST;
        }
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
        $this->scheme = $this->getSchemeAndValue()[0];

        return $this;
    }

    private function getAuthenticationTokens()
    {
        $encodedAuthenticationTokens = $this->getEncodedAuthenticationToken();
        $decoded = base64_decode($encodedAuthenticationTokens);

        $tokens = explode(":", $decoded, 2);

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


        if(!isset($this->headers['Authorization']))
            throw new \InvalidArgumentException('You need to supply the authorization header as part of your request.
                                                 Please add the header and try again.');

        return explode(" ", $this->headers['Authorization'], 2);
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