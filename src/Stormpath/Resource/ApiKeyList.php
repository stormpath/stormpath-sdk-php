<?php
/**
 * Created by IntelliJ IDEA.
 * User: diegomunguia
 * Date: 13/07/15
 * Time: 10:14 PM
 */

namespace Stormpath\Resource;


use Stormpath\Stormpath;

class ApiKeyList extends AbstractCollectionResource
{

    function getItemClassName()
    {
        return Stormpath::API_KEY;
    }
}