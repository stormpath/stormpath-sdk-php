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


namespace Stormpath\Oauth;


use Stormpath\Resource\AccountStore;
use Stormpath\Resource\Resource;

class PasswordGrantAuthenticationAttempt extends Resource
{

    const LOGIN                 = 'username';
    const PASSWORD              = 'password';
    const ACCOUNT_STORE_HREF    = 'accountStore';
    const GRANT_TYPE            = 'grant_type';


    /**
     * @param string $login
     * @return $this
     */
    public function setLogin($login) {
        $this->setProperty(self::LOGIN, $login);

        return $this;
    }


    /**
     * @param string $password
     * @return $this
     */
    public function setPassword($password) {
        $this->setProperty(self::PASSWORD, $password);

        return $this;
    }

    /**
     * @param string $grantType
     * @return $this
     */
    public function setGrantType($grantType) {
        $this->setProperty(self::GRANT_TYPE, $grantType);

        return $this;
    }

    /**
     * @param AccountStore $accountStore
     */
    public function setAccountStore(AccountStore $accountStore) {
        $this->setProperty(self::ACCOUNT_STORE_HREF, $accountStore->getHref());

        return $this;
    }

    /**
     * @return string
     */
    public function getLogin() {
        return $this->getProperty(self::LOGIN);
    }

    /**
     * @return string
     */
    public function getPassword() {
        return $this->getProperty(self::PASSWORD);
    }

    /**
     * @return string
     */
    public function getGrantType(){
        return $this->getProperty(self::GRANT_TYPE);
    }

    /**
     * @return string
     */
    public function getAccountStoreHref() {
        return $this->getProperty(self::ACCOUNT_STORE_HREF);
    }
}