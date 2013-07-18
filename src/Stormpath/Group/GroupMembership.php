<?php
/**
 *
 */

namespace Stormpath\Group;

use Stormpath\Resource\Deletable;
use Stormpath\Resource\Resource;

interface GroupMembership extends Resource, Deletable
{
     public function getAccount();

     public function getGroup();
}