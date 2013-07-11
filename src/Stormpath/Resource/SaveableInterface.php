<?php

namespace Stormpath\Resource;

interface SaveableInterface
{
    public function save();
    public function delete();
}