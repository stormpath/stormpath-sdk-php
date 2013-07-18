<?php

namespace Stormpath\DataStore;

interface DataStore
{
	public function instantiate($clazz);

	public function getResource($href, $clazz);
}