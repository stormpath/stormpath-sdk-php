<?php
/**
 *
 */

namespace Stormpath\Lang;

class InstantiationException extends \RuntimeException
{
     public function __construct($string, $throwable)
     {
           parent::__construct($string, $throwable);
     }
}

