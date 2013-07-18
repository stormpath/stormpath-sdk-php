<?php
/**
 *
 */

namespace Stormpath\Group;

use Stormpath\Query\Options;

interface GroupOptions extends Options
{
     public function withDirectory();

     public function withTenant();

     public function withAccounts($limitoffset);

     public function withAccountMemberships($limitoffset);
}