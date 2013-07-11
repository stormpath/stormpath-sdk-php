<?php

namespace Stormpath\Resource;

class Page
{
    private $offset;
    private $limit;
    private $items;
    
    public function __construct($offset, $limit, array $items)
    {
        $this->items = $items;
        $this->limit = $limit;
        $this->offset = $offset;
    }
    
    public function getItems()
    {
        return $this->items;
    }
    
    public function getLimit()
    {
        return $this->limit;
    }
    
    public function getOffset()
    {
        return $this->offset;
    }
}