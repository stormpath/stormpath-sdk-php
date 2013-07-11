<?php

namespace Stormpath\DataStore;

use Stormpath\DataStore\ResourceFactory;
use Stormpath\DataStore\InternalDataStore;

class DefaultResourceFactory implements ResourceFactory
{
    private $dataStore;

    const RESOURCE_PATH = 'Stormpath\Resource\\';
    const AUTHC_PATH = 'Stormpath\Authc\\';

    public function __construct(InternalDataStore $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    public function instantiate($className, array $constructorArgs)
    {
        $class = new \ReflectionClass($this->qualifyClassName($className));

        array_unshift($constructorArgs, $this->dataStore);

        return $class->newInstanceArgs($constructorArgs);
    }

    private function qualifyClassName($className)
    {
        if (strpos($className, self::RESOURCE_PATH) === false
            and strpos($className, self::AUTHC_PATH) === false)
        {
            return self::RESOURCE_PATH .$className;
        }

        return $className;

    }

}
