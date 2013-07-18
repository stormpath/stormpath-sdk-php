<?php
/**
 *
 */

namespace Stormpath\Resource;

interface CollectionResource extends Resource, \Iterator
{
     public function getOffset();

     public function getLimit();
}