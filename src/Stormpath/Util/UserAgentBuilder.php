<?php


namespace Stormpath\Util;


class UserAgentBuilder {




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


        if(\Stormpath\Client::$integration) {
            $userAgent[] = \Stormpath\Client::$integration;
        }
        $userAgent[] = 'stormpath-sdk-php/'. Version::SDK_VERSION;
        $userAgent[] = 'php/' . $this->phpVersion;
        $userAgent[] = $this->osName .'/'. $this->osVersion;

        return implode(' ', $userAgent);

    }

    private function validateUserAgentProperties()
    {
        if(!$this->phpVersion) throw new \InvalidArgumentException('Please provide a PHP Version.');
        if(!$this->osName) throw new \InvalidArgumentException('Please provide an Operating System Name.');
        if(!$this->osVersion) throw new \InvalidArgumentException('Please provide an Operating System Version.');
    }



}