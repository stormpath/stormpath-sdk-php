<?php


namespace Stormpath\Oauth;


use Stormpath\Resource\AccountStore;

/**
 * Class PasswordGrantRequest
 * @package Stormpath\Oauth
 * @since 1.11.0.beta
 */
class PasswordGrantRequest
{
    /**
     * @var
     */
    private $login;

    /**
     * @var
     */
    private $password;

    /**
     * @var
     */
    private $accountStore;

    /**
     * @var string
     */
    private $grant_type = 'password';

    public function __construct($login, $password)
    {

        $this->login = $login;
        $this->password = $password;
    }

    /**
     * @param AccountStore $accountStore
     * @return $this
     */
    public function setAccountStore(AccountStore $accountStore)
    {
        $this->accountStore = $accountStore;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return mixed
     */
    public function getAccountStore()
    {
        return $this->accountStore;
    }

    /**
     * @return string
     */
    public function getGrantType()
    {
        return $this->grant_type;
    }
}