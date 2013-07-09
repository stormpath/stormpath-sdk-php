<?php

namespace Stormpath\DataStore;

use Stormpath\Resource\Resource;

interface InternalDataStore extends DataStore
{
    public function create($parentHref, Resource $resource, $returnType);

    public function save(Resource $resource, $returnType = null);

    public function delete(Resource $resource);

}