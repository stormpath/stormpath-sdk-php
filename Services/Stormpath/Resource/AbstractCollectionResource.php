<?php


abstract class Services_Stormpath_Resource_AbstractCollectionResource
    extends Services_Stormpath_Resource_Resource
    implements IteratorAggregate
{
    const OFFSET = "offset";
    const LIMIT = "limit";
    const ITEMS = "items";

}
