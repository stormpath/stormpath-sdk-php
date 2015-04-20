<?php namespace Stormpath\Util;


use Stormpath\DataStore\DataStore;

class NonceStore {

    private $cache;

    public static function generateNonce()
    {
        return UUID::v4();
    }

    public function __construct(DataStore $dataStore)
    {
        $this->cache = $dataStore->getCacheManager()->getCache();
    }

    public function getNonce($nonce)
    {
        return $this->cache->get('nonce_'.$nonce);
    }

    public function putNonce($nonce)
    {
        $this->cache->put('nonce_'.$nonce,$nonce,1);
    }

}