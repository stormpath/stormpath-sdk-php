<?php
/**
 *
 */

namespace Stormpath\Query;

interface Criteria
{
     public function add($criterion);

     public function annd($criterion);

     public function ascending();

     public function descending();

     public function offsetBy($offset);

     public function limitTo($limit);

     public function isEmpty();
}