<?php

namespace Stormpath\DataStore;

interface DataStore
{
    public function instantiate($className, stdClass $properties = null);

    public function  getResource($href, $className);

}