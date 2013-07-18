<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wmefteh
 * Date: 7/17/13
 * Time: 11:49 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Stormpath\Directory;


use Stormpath\Query\Options;

interface AccountStoreOptions extends Options {

        public function withAccounts($limitoffset);

        public function withGroups($limitoffset);

        public function withTenant();
}