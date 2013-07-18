<?php
/**
 */

namespace Stormpath\Application;

use Stormpath\Resource\Deletable;
use Stormpath\Resource\Resource;
use Stormpath\Resource\Saveable;

interface Application extends Resource, Saveable, Deletable
{
     public function getName();

     public function setName($name);

     public function getDescription();

     public function setDescription($description);

     public function getStatus();

     public function setStatus($status);

     public function getAccounts($criteria);

     public function getGroups($criteria);

     public function getTenant();

     public function sendPasswordResetEmail($accountUsernameOrEmail);

     public function verifyPasswordResetToken($token);

     public function authenticateAccount ($request);
}