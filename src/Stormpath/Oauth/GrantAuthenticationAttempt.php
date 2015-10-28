<?php


namespace Stormpath\Oauth;


use Stormpath\Resource\AccountStore;
use Stormpath\Resource\Resource;

class GrantAuthenticationAttempt extends Resource
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