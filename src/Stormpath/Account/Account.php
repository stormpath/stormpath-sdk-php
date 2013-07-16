<?php
/*
*
*
*/

namespace Stormpath\Account;

interface Account extends Resource, Savable
{
	public function getUsername();

	public function setUsername($username);

	public function getEmail();

	public function setEmail($email);

	public function setPassword($password);

	public function getGivenName();

	public function setGivenName($givenName);

	public function getMiddleName();

	public function setMiddleName($middleName);

	public function getSurname();

	public function setSurname($surname);

	public function getStatus();

	public function setStatus($status);

	public function getGroups($criteria = null);

	public function getDirectory();

	public function getTenant();

	public function getGroupMemberShips();

	public function addGroup(Group $group);

	public function getEmailVerificationToken();
}