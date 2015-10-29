<?php


namespace Stormpath\Resource;


use Stormpath\Client;
use Stormpath\Stormpath;

class AccessToken extends InstanceResource implements Deletable
{
    const ACCOUNT                       = 'account';
    const APPLICATION                   = 'application';
    const EXPANDED_JWT                  = 'expandedJwt';
    const JWT                           = 'jwt';
    const TENANT                        = 'tenant';

    const PATH                          = 'accessTokens';

    /**
     * @param $href
     * @param array $options
     * @return mixed
     */
    public static function get($href, array $options = [])
    {
        return Client::get($href, Stormpath::ACCESS_TOKEN, self::PATH, $options);
    }

    /**
     * @param array $options
     * @return \Stormpath\Resource\Account
     */
    public function getAccount(array $options = [])
    {
        return $this->getResourceProperty(self::ACCOUNT, Stormpath::ACCOUNT, $options);
    }

    /**
     * @param array $options
     * @return \Stormpath\Resource\Application
     */
    public function getApplication(array $options = [])
    {
        return $this->getResourceProperty(self::APPLICATION, Stormpath::APPLICATION, $options);
    }

    /**
     * @return \stdClass
     */
    public function getExpandedJwt()
    {
        return $this->getProperty(self::EXPANDED_JWT);
    }

    /**
     * @return string
     */
    public function getJwt()
    {
        return $this->getProperty(self::JWT);
    }

    /**
     * @param array $options
     * @return \Stormpath\Resource\Tenant
     */
    public function getTenant(array $options = [])
    {
        return $this->getResourceProperty(self::TENANT, Stormpath::TENANT, $options);
    }

    /**
     * @return null
     */
    public function delete()
    {
        $this->getDataStore()->delete($this);
    }
}