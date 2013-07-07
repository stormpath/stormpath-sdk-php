<?php
/*
 * Create the client by giving the APIid and the Secret key
 *
 * DevNotes:  keys of 'id' and 'secret' @ http://www.stormpath.com/docs/rest/api#Base
 */

namespace Stormpath\Service;

use Stormpath\Client\ApiKey;
use Stormpath\Client\Client;

class StormpathService
{
    const BASEURI = 'https://api.stormpath.com/v1/';

    public function register($name, $description = '', $status = 'enabled')
    {
        switch ($status) {
            case 'enabled':
            case 'disabled':
                break;
            default:
                throw new \Exception('Invalid application status');
        }

        $client = self::getHttpClient();
        $client->setUri(self::BASEURI . '/applications');
        $client->setMethod('POST');
        $client->setRawBody(Json::encode(array(
            'name' => $name,
            'description' => $description,
            'status' => $status,
        )));


        return Json::decode($client->send()->getBody());
    }

    /*public function InstantiateClient($accessId, $secret, $baseURL)
    {
        $apikey = new ApiKey($accessId,$secret);
		//$apikey = array('id' => $accessId , 'secret' => $secret);
		return new Client($apikey, $baseURL);
    }
	*/


}
