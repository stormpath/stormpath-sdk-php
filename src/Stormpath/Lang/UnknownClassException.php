<?php
/**
 *
 */

namespace Stormpath\Lang;

class UnknownClassException extends \RuntimeException
{
    public function __construct($message, $cause)
    {
        if ($message==null && $cause == null) {
                  parent::__construct();
            }
        elseif ($cause == null) {
                parent::__construct($message);
            }
        elseif ($message = null) {
                parent::__construct($cause);
            }
        else {
                parent::__construct($message, $cause);
            }
    }
}