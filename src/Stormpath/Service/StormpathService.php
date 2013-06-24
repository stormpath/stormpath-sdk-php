<?php
/*
 * Create the client by giving the APIid and the Secret key
 *
 * DevNotes:  keys of 'id' and 'secret' @ http://www.stormpath.com/docs/rest/api#Base
 */

namespace Stormpath\Service;

use Stormpath\Client\ApiKey;
use Stormpath\Http\Client\Adapter\Digest;
use Stormpath\Http\Client\Adapter\Basic;
use Zend\Http\Client;
use Zend\Json\Json;

class StormpathService
{
    const BASEURI = 'https://api.stormpath.com/v1';

    private static $id;
    private static $secret;
    private static $httpClient;

    public static function getId()
    {
        return self::$id;
    }

    private static function setId($value)
    {
        self::$id = $value;
    }

    public static function getSecret()
    {

        return self::$secret;
    }

    private static function setSecret($value)
    {
        self::$secret = $value;
    }

    public static function getHttpClient()
    {
        return self::$httpClient;
    }

    public static function setHttpClient(Client $value)
    {
        self::$httpClient = $value;
    }

    public static function configure($id, $secret = null)
    {
        self::setId($id);
        self::setSecret($secret);

        // Set default http client; overwriteable after configuration
        $client = new Client();
        $adapter = new Digest();
        $client->setAdapter($adapter);
        self::setHttpClient($client);
    }

    public static function register($name, $description = '', $status = 'enabled')
    {
        switch ($status) {
            case 'enabled':
            case 'disabled':
                break;
            default:
                throw new \Exception('Invalid application status');
        }

        $client = self::getHttpClient();
        $client->setUri(self::BASEURI . '/applications');
        $client->setMethod('POST');
        $client->setOptions(array('sslverifypeer' => false));
        $client->setRawBody(Json::encode([
            'name' => $name,
            'description' => $description,
            'status' => $status,
        ]));

        return Json::decode($client->send()->getBody());
    }

    public static function createClient($accessId, $secretKey)
    {
        ApiKey::setAccessId($accessId);
        ApiKey::setSecretKey($secretKey);
    }

}