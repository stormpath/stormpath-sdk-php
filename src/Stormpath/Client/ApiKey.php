<?php
/**
 *
 */

namespace Stormpath\Client;

interface ApiKey
{
    public function getId();

    public function getSecret();
}