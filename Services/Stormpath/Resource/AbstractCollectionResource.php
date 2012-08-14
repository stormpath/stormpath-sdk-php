<?php


abstract class Services_Stormpath_Resource_AbstractCollectionResource
    extends Services_Stormpath_Resource_Resource
    implements IteratorAggregate
{
    const OFFSET = "offset";
    const LIMIT  = "limit";
    const ITEMS  = "items";

    abstract function getItemClassName();

    protected function getOffset()
    {
        return $this->getProperty(self::OFFSET);
    }

    protected function getLimit()
    {
        return $this->getProperty(self::LIMIT);
    }

    protected function getCurrentPage()
    {
        $values = $this->getProperty(self::ITEMS);
        $items = $this->toResourceArray($values);

        return new Services_Stormpath_Resource_Page($this->getOffset(), $this->getLimit(), $items);
    }

    protected function toResource($className, stdClass $properties)
    {
        return $this->getDataStore()->instantiate($className, $properties);
    }

    private function toResourceArray(array $values)
    {
        $className = $this->getItemClassName();
        $resourceArray = array();

        $i = 0;
        foreach($values as $value)
        {
            $resource = $this->toResource($className, $value);
            $resourceArray[$i] = $resource;
            $i++;
        }

        return $resourceArray;

    }

    public function getIterator()
    {
        return new ArrayIterator($this->getCurrentPage()->getItems());
    }
}
