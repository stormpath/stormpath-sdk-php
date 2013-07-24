<?php
/*
 * Create the client by giving the APIid and the Secret key
 *
 * DevNotes:  keys of 'id' and 'secret' @ http://www.stormpath.com/docs/rest/api#Base
 */

namespace Stormpath\Service;

use Stormpath\Persistence\ResourceManager;
use Stormpath\Http\Client\Adapter\Digest;
use Stormpath\Http\Client\Adapter\Basic;
use Zend\Http\Client;
use Zend\Json\Json;
use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\StorageInterface;

class StormpathService
{
    private static $id;
    private static $secret;
    private static $httpClient;
    private static $cache;

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

    public static function getCache()
    {
        return self::$cache;
    }

    public static function setCache(StorageInterface $cache)
    {
        self::$cache = $cache;
    }

    public static function configure($id, $secret)
    {
        self::setId($id);
        self::setSecret($secret);

        // Set default http client; overwriteable after configuration
        $client = new Client(null, array('keepalive' => true));
        $adapter = new Digest();
        $client->setAdapter($adapter);
        self::setHttpClient($client);
        self::setCache(StorageFactory::adapterFactory('memory'));
    }

    public static function getResourceManager()
    {
        $resourceManager = new ResourceManager();
        $resourceManager->setHttpClient(self::getHttpClient());
        $resourceManager->setCache(self::getCache());

        return $resourceManager;
    }
}
