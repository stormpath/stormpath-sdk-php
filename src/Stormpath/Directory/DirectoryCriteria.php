<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wmefteh
 * Date: 7/17/13
 * Time: 11:55 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Stormpath\Directory;


use Stormpath\Query\Criteria;

interface DirectoryCriteria extends Criteria, DirectoryOptions{

        public function orderByName();

        public function orderByDescription();

        public function orderByStatus();

}