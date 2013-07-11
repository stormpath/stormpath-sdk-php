<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vganesh
 * Date: 7/11/13
 * Time: 12:10 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Stormpath\Cache;

use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\StorageInterface;

class Cache
{
	const DEFAULT_STORE = "memory";
	const DEFAULT_TTL_SECONDS = 300; //time to live
	const DEFAULT_TTI_SECONDS = 300; // time to idle

	private $stats;
	private $ttlSeconds;
	private $ttiSeconds;
	private $storeOptions;
	private $store;
	private $cache;
	private $entry;

	public function _construct($options = array())
	{
		if(!$options['ttl_seconds']) {
			$this->ttlSeconds = DEFAULT_TTL_SECONDS;
		}

		if(!$options['ttl_seconds']) {
			$this->ttiSecondseconds = DEFAULT_TTI_SECONDS;
		}
		else
		{
			$this->ttlSeconds = $options['ttlSeconds'];
			$this->ttiSeconds = $options['ttiSeconds'];
		}

		$this->storeOptions = $options['storeOptions'];

		if(!$options['store']) {
			$this->store = DEFAULT_STORE;
			$this->cache  = StorageFactory::adapterFactory($this->store);
		}
		else {
			$this->storeOptions = $options['store'];
			$this->cache  = StorageFactory::adapterFactory($this->store);
		}

		$this->stats = new CacheStats();

	}

	public function get($key)
	{
		$entry = new CacheEntry();
		if($entry == $this->get($key)) {
			if($entry->expired($this->ttlSeconds, $this->ttiSeconds)) {
				$this->store->miss(true);
				$this->store->delete(k);
			}
			else {
				$this->stats->hit();
				$entry->touch();
				$entry->value();
			}
		}
		else {
				$this->stats->miss();
			 }

	}

	public function put($key, $value)
	{
		$this->entry = new CacheEntry($value);
		$this->store->put($key, $this->entry);
		$this->stats->put;
	}

	public function delete($key)
	{
		$this->store->delete($key);
		$this->stats->delete;
	}

	public function clear()
	{
		$this->store->clear();
	}

	public function size()
	{
		$this->stats->size();
	}
	public function getCache()
	{
		return $this->cache;
	}

	public function setCache(StorageInterface $cache)
	{
		$this->cache = $cache;
	}
}