<?php
/*
 * Create the client by giving the APIid and the Secret key
 *
 * DevNotes:  keys of 'id' and 'secret' @ http://www.stormpath.com/docs/rest/api#Base
 */

namespace Stormpath\Service;

use Stormpath\ResourceManager;
use Stormpath\Http\Client\Adapter\Digest;
use Stormpath\Http\Client\Adapter\Basic;
use Zend\Http\Client;
use Zend\Json\Json;

class StormpathService
{
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
        $value->setOptions(array('sslverifypeer' => false));
        self::$httpClient = $value;
    }

    public static function configure($id, $secret)
    {
        self::setId($id);
        self::setSecret($secret);

        // Set default http client; overwriteable after configuration
        $client = new Client();
        $adapter = new Basic();
        $client->setAdapter($adapter);
        self::setHttpClient($client);
    }

    public static function getResourceManager()
    {
        $resourceManager = new ResourceManager();
        $resourceManager->setHttpClient(self::getHttpClient());

        return $resourceManager;
    }

/*
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
        $client->setRawBody(Json::encode(array(
            'name' => $name,
            'description' => $description,
            'status' => $status,
        )));

        try {
            $response = $client->send();
        } catch (\Exception $e) {

        }
        return Json::decode($client->send()->getBody());
    }
*/
}
