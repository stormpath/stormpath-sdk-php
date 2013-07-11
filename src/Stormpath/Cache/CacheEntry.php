<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vganesh
 * Date: 7/11/13
 * Time: 12:34 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Stormpath\Cache;

use Datetime;

class CacheEntry
{
	private $value;
	private $createdAt;
	private $lastAccessedAt;

	public function construct($value)
	{
		$this->value = $value;
		$this->createdAt = new Datetime();
		$this->lastAccessedAt = $this->createdAt;

	}

	public function touch()
	{
		$this->lastAccessedAt = time();
	}

	public function expired($ttlSeconds, $ttiSeconds)
	{
		$now = new Datetime();
		if(($now > $this->createdAt + $ttlSeconds) || $now > ($this->lastAccessedAt + $ttiSeconds)) {
			return true;
		}
		else {
			return false;
		}
	}

	public function toHash()
	{
		return array('value' => $this->value,
					     'createdAt' => $this->createdAt,
			                 'lastAccessedAt' => $this->lastAccessedAt );
	}

	//use zend cache here
}