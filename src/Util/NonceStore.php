<?php namespace Stormpath\Util;


use Stormpath\DataStore\DataStore;

class NonceStore {
    // @codeCoverageIgnoreStart
    private $pool;

    public static function generateNonce()
    {
        return UUID::v4();
    }

    public function __construct(DataStore $dataStore)
    {
        $this->pool = $dataStore->getCachePool();
    }

    public function getNonce($nonce)
    {
        $item = $this->pool->getItem('nonce_'.$nonce);
        return $item->get();
    }

    public function putNonce($nonce)
    {
        $item = $this->pool->getItem('nonce_'.$nonce);
        $item->set($nonce);
        $item->expiresAfter(60);
        $this->pool->save($item);
    }
    // @codeCoverageIgnoreStart
}
