<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vganesh
 * Date: 7/2/13
 * Time: 8:09 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Stormpath\Resource;

use Zend\Http\Client;
use Zend\Json\Json;
use Stormpath\Service\StormpathService as Stormpath;

class Group
{
    public static function read($groupID)
    {
        $client = Stormpath::getHttpClient();
        $client->setUri(Stormpath::BASEURI . '/groups/' . urlencode($groupID));
        $client->setMethod('GET');

        return Json::decode($client->send()->getBody());
    }



}
