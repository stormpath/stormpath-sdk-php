<?php
/**
 *
 */

namespace Stormpath\Authc;

use Stormpath\Resource\Resource;

interface AuthenticationResult extends Resource
{
    public function getAccount();
}