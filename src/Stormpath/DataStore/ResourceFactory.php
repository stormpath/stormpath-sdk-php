<?php

namespace Stormpath\DataStore;

interface ResourceFactory
{
    public function instantiate($className, array $constructorArgs);
}
