<?php
/*
*
*/

namespace Stormpath\Account;

interface AccountOptions extends Options
{
	public function withDirectory();

	public function withTenant();

	public function withGroups($limit, $offset = null);

	public function withGroupMemberShips($limit, $offset = null);

}