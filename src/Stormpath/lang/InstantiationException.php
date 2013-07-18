<?php
/**
 *
 */

namespace Stormpath\lang;

class InstantiationException extends \RuntimeException
{

    public function __construct($s, $t)
    {
        parent::__constrcut($s,$t);
    }
}
