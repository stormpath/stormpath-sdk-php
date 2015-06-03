<?php


namespace Stormpath\Util;

use Stormpath\Exceptions\UserAgentException;

class UserAgentBuilder {

    /**
     * @var string
     */
    protected $sdkName = 'stormpath-sdk-php';

    /**
     * @var string
     */
    protected $sdkVersion = Version::SDK_VERSION;

    /**
     * @var bool
     */
    protected $hasIntegration = false;

    /**
     * @var
     */
    protected $phpVersion;

    /**
     * @var
     */
    protected $osVersion;

    /**
     * @var
     */
    protected $osName;

    /**
     * @codeCoverageIgnore
     * @param IntegrationUserAgent $userAgent
     * @return $this
     */
    // Todo: Allow setting integration information to the UserAgent
    // @codeCoverageIgnoreStart
    public function setIntegration(IntegrationUserAgent $userAgent)
    {

    }
    // @codeCoverageIgnoreEnd

    /**
     * Set the SDK Name
     * @param string $sdkName
     * @return $this
     */
    public function setSdkName($sdkName = 'stormpath-sdk-php')
    {
        $this->sdkName = $sdkName;
        return $this;
    }

    /**
     * Set the SDK Version
     * @param string $sdkVersion
     * @return $this
     */
    public function setSdkVersion($sdkVersion = Version::SDK_VERSION)
    {
        $this->sdkVersion = $sdkVersion;
        return $this;
    }

    /**
     * Set the PHP Version
     * @param $version
     * @return $this
     */
    public function setPhpVersion($version)
    {
        $this->phpVersion = $version;
        return $this;
    }

    /**
     * Set the Operating System Name
     * @param $os
     * @return $this
     */
    public function setOsName($os)
    {
        $this->osName = $os;
        return $this;
    }

    /**
     * Set the Operating System Version
     * @param $version
     * @return $this
     */
    public function setOsVersion($version)
    {
        $this->osVersion = $version;
        return $this;
    }

    /**
     * Build your User Agent.  This will build in an order required by Stormpath.
     * @return string
     * @throws UserAgentException
     */
    public function build()
    {
        $this->validateUserAgentProperties();

        $userAgent = array();

        // Todo: add in the integration information for the UserAgent
//        if($this->hasIntegration) {
//            $userAgent[] = $this->runtimeIntegration;
//            $userAgent[] = $this->runtime;
//        }

        $userAgent[] = $this->sdkName .'/'. $this->sdkVersion;
        $userAgent[] = 'php/' . $this->phpVersion;
        $userAgent[] = $this->osName .'/'. $this->osVersion;

        return implode(' ', $userAgent);

    }

    private function validateUserAgentProperties()
    {
        if(!$this->sdkName) throw new UserAgentException('Please provide a SDK Name.');
        if(!$this->sdkVersion) throw new UserAgentException('Please provide a SDK Version.');
        if(!$this->phpVersion) throw new UserAgentException('Please provide a PHP Version.');
        if(!$this->osName) throw new UserAgentException('Please provide an Operating System Name.');
        if(!$this->osVersion) throw new UserAgentException('Please provide an Operating System Version.');
    }



}