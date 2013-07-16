<?php
/*
*
*/

namespace Stormpath\Account;

interface AccountCriteria extends Criteria
{
	public function orderByEmail();

	public function orderByUsername();

	public function orderByGivenName();

	public function orderByMiddleName();

	public function orderBySurName();

	public function orderByStatus();
}