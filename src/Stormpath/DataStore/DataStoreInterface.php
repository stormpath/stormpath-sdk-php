<?php
/**
 *
 */

interface DataStoreInterface
{
	public function instantiate($classname, $properties);

	public function getResource($href, $classname, $query);

	public function create($parenthref, $resouce, $returnType);

	public function save($resource, $classname);

	public function delete($resource);

}