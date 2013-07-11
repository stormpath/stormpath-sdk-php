<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vganesh
 * Date: 7/11/13
 * Time: 12:24 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Stormpath\Cache;

class CacheStats
{
	private $puts;
	private $hits;
	private $misses;
	private $expirations;
	private $size;

	public function __construct()
	{
		$this->puts = $this->hits = $this->misses = $this->expirations = $this->size = 0;
	}

	public function put()
	{
		$this->puts += 1;
	}

	public function hit()
	{
		$this->hits += 1;
	}

	public function miss($expired = false)
	{
		$this->misses += 1;
		if($expired)
		{
			$this->expirations += 1;
		}
	}

	public function delete()
	{
		if($this->size > 0)
		{
			$this->size -= 1;
		}
	}

	public function summary()
	{
		return array($this->puts, $this->hits, $this->misses, $this->expirations, $this->size);
	}

}