<?php
/**
 * Internal DataStore used for implementation purposes only.  Not intended to be called by SDK end users!
 * <p/>
 * <b>WARNING: This API CAN CHANGE AT ANY TIME, WITHOUT NOTICE.  DO NOT DEPEND ON IT.</b>
 *
 */

interface Services_Stormpath_DataStore_InternalDataStore
    extends Services_Stormpath_DataStore_DataStore
{

    public function instantiateWithProperties($class, array $properties);

    public function create(String $parentHref,
                           Services_Stormpath_Resource_Resource $resource,
                           $returnType);

    public function save(Services_Stormpath_Resource_Resource $resource,
                         $returnType = null);

    public function delete(Services_Stormpath_Resource_Resource $resource);

}