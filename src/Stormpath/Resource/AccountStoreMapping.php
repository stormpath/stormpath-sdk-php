<?php

/*
 * Copyright 2013 Stormpath, Inc.
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

namespace Stormpath\Resource;


use Stormpath\Stormpath;

class AccountStoreMapping extends InstanceResource implements Deletable {

    const APPLICATION               = "application";
    const ACCOUNT_STORE             = "accountStore";
    const LIST_INDEX                = "listIndex";
    const IS_DEFAULT_ACCOUNT_STORE  = "isDefaultAccountStore";
    const IS_DEFAULT_GROUP_STORE    = "isDefaultGroupStore";

    public function getApplication(array $options = array()) {

        return $this->getResourceProperty(self::APPLICATION, Stormpath::APPLICATION, $options);
    }

    public function getAccountStore(array $options = array()) {

        $accountStore = $this->getResourceProperty(self::ACCOUNT_STORE, Stormpath::DIRECTORY, $options);

        if($accountStore) {

            $href = $accountStore->getHref();

            if (stristr($href, 'groups')) {
                $propertyNames = $accountStore->getPropertyNames();
                $accountStoreProperties = new \stdClass();

                foreach($propertyNames as $name) {

                    $accountStoreProperties->$name = $accountStore->getProperty($name);
                }

                $accountStore = $this->getDataStore()->instantiate(Stormpath::GROUP, $accountStoreProperties);
            }
        }

        return $accountStore;
    }

    public function getListIndex() {

        return $this->getProperty(self::LIST_INDEX);
    }

    public function getDefaultAccountStore() {

        return $this->getProperty(self::IS_DEFAULT_ACCOUNT_STORE);
    }

    public function isDefaultAccountStore() {

        return $this->getDefaultAccountStore();
    }

    public function getDefaultGroupStore() {

        return $this->getProperty(self::IS_DEFAULT_GROUP_STORE);
    }

    public function isDefaultGroupStore() {

        return $this->getDefaultGroupStore();
    }

    public function setApplication(Application $application) {

        $this->setResourceProperty(self::APPLICATION, $application);
    }

    public function setAccountStore(AccountStore $accountStore) {

        $this->setResourceProperty(self::ACCOUNT_STORE, $accountStore);
    }

    public function setListIndex($listIndex) {

        $this->setProperty(self::LIST_INDEX, $listIndex);
    }

    public function setDefaultAccountStore($IsDefaultAccountStore) {

        $this->setProperty(self::IS_DEFAULT_ACCOUNT_STORE, $IsDefaultAccountStore);
    }

    public function setDefaultGroupStore($IsDefaultGroupStore) {

        $this->setProperty(self::IS_DEFAULT_GROUP_STORE, $IsDefaultGroupStore);
    }

    public function delete() {

        $this->getDataStore()->delete($this);
    }
}