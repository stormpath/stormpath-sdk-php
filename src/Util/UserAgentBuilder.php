<?php
/**
 * Copyright 2017 Stormpath, Inc.
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
 *
 */

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