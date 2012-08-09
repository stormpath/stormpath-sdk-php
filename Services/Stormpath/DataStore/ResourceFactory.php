<?php

interface Services_Stormpath_DataStore_ResourceFactory
{

    public function instantiate($className, array $constructorArgs);

}
