<?php

namespace Stormpath\Resource;

/*
 * Copyright 2016 Stormpath, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use Stormpath\Stormpath;

class ProviderAccountResult extends Resource
{
    const ACCOUNT       = 'account';
    const NEW_ACCOUNT   = 'isNewAccount';
    
    public function __construct($dataStore = null, \stdClass $properties = null, array $options = array())
    {
    	parent::__construct($dataStore, $properties, $options);
    	
    	if ($properties != null)
    	{
			$this->setProperty(self::NEW_ACCOUNT, $properties->newAccount);
			unset($properties->newAccount);
			$account = $this->getDataStore()->instantiate(Stormpath::ACCOUNT, $properties);
			$this->setProperty(self::ACCOUNT, $account);    		
    	}
    }

    public function getAccount(array $options = array())
    {
        return $this->getProperty(self::ACCOUNT);
    }

    public function isNewAccount()
    {
        return $this->getProperty(self::NEW_ACCOUNT);
    }

}