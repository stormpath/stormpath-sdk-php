<?php

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

namespace Stormpath\Resource;


use Stormpath\Client;
use Stormpath\DataStore\InternalDataStore;
use Stormpath\Stormpath;

class AccountStoreMapping extends InstanceResource implements Deletable {

    const APPLICATION               = "application";
    const ORGANIZATION              = "organization";
    const ACCOUNT_STORE             = "accountStore";
    const LIST_INDEX                = "listIndex";
    const IS_DEFAULT_ACCOUNT_STORE  = "isDefaultAccountStore";
    const IS_DEFAULT_GROUP_STORE    = "isDefaultGroupStore";

    const PATH                      = "accountStoreMappings";

    public static function get($href, array $options = array())
    {
        return Client::get($href, Stormpath::ACCOUNT_STORE_MAPPING, self::PATH, $options);
    }

    public static function instantiate($properties = null)
    {
        return Client::instantiate(Stormpath::ACCOUNT_STORE_MAPPING, $properties);
    }

    public static function create($properties, array $options = array())
    {
        $accountStoreMapping = $properties;

        if (!($accountStoreMapping instanceof AccountStoreMapping))
        {
            $accountStoreMapping = self::instantiate($properties);
        }

        $application = $accountStoreMapping->application;

        if (!($application instanceof Application))
        {
            throw new \InvalidArgumentException('The application property must be an existing application resource and instance of "Stormpath\Resource\Application".');
        }

        return $application->createAccountStoreMapping($accountStoreMapping, $options);
    }

    public function getApplication(array $options = array()) {

        return $this->getResourceProperty(self::APPLICATION, Stormpath::APPLICATION, $options);
    }

    public function getOrganization(array $options = array()) {

        return $this->getResourceProperty(self::ORGANIZATION, Stormpath::ORGANIZATION, $options);
    }

    public function getAccountStore(array $options = array()) {

        $accountStore = $this->getResourceProperty(self::ACCOUNT_STORE, Stormpath::DIRECTORY, $options);

        if($accountStore) {

            $href = $accountStore->getHref();

            if (stristr($href, Group::PATH)) {
                $propertyNames = $accountStore->getPropertyNames();
                $accountStoreProperties = new \stdClass();

                foreach($propertyNames as $name) {

                    $accountStoreProperties->$name = $accountStore->getProperty($name);
                }

                $accountStore = $this->getDataStore()->instantiate(Stormpath::GROUP, $accountStoreProperties, $options);
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

    public function setAccountStore(AccountStore $accountStore) {

        $this->setResourceProperty(self::ACCOUNT_STORE, $accountStore);
    }

    public function setApplication(Application $application) {

        $this->setResourceProperty(self::APPLICATION, $application);
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

    /**
     * THIS IS NOT PART OF THE STORMPATH PUBLIC API.  SDK end-users should not call it - it could be removed or
     * changed at any time.  It has the public modifier only as an implementation technique to be accessible to other
     * resource implementations.
     *
     * @param $accountStoreMapping the account store mapping to create.
     * @param $application the application to associate with the account store mapping.
     * @param $dataStore the data store used to create the account store mapping.
     * @param $options the options to pass to the group mapping creation.
     * @return the created AccountStoreMapping instance.
     */
    public static function _create(AccountStoreMapping $accountStoreMapping, Application $application, InternalDataStore $dataStore, array $options = array())
    {
        //TODO: enable auto discovery
        $href = "/" . self::PATH;

        // properly setting the resource properties
        $accountStoreMapping->setResourceProperty(self::APPLICATION, $application);
        $accountStoreMapping->accountStore = $accountStoreMapping->accountStore;

        return $dataStore->create($href, $accountStoreMapping, Stormpath::ACCOUNT_STORE_MAPPING, $options);
    }
}