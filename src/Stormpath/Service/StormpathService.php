<?php
/*
 * Create the client by giving the APIid and the Secret key
 */

namespace Stormpath\Service;

use Stormpath\Client\ApiKey;

final class StormpathService
{

    const BASEURI = 'https://api.stormpath.com/v1';

    public static function createClient($accessId, $secretKey)
    {
        ApiKey::setAccessId($accessId);
        ApiKey::setSecretKey($secretKey);
    }

}