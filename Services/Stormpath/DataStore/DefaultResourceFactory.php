<?php


class Services_Stormpath_DataStore_DefaultResourceFactory
    implements Services_Stormpath_DataStore_ResourceFactory
{

    private $dataStore;

    const RESOURCE_PATH = 'Services_Stormpath_Resource_';
    const AUTHC_PATH = 'Services_Stormpath_Authc_';

    public function __construct(Services_Stormpath_DataStore_InternalDataStore $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    public function instantiate($className, array $constructorArgs)
    {
        $class = new ReflectionClass($this->qualifyClassName($className));

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
