<?php
/**
 *
 */

namespace Stormpath\Group;

use Stormpath\Query\Criteria;

interface GroupCriteria extends Criteria, GroupOptions
{
        public function orderByName();

        public function orderByDescription();

        public function orderByStatus();
}