<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wmefteh
 * Date: 7/17/13
 * Time: 11:23 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Stormpath\Application;

use Stormpath\Query\Criteria;

interface ApplicationCriteria extends Criteria, ApplicationOptions
{
    public function orderByName();

    public function orderByDescription();

    public function orderByStatus();
}