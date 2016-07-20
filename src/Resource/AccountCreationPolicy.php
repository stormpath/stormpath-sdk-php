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

use Stormpath\Stormpath;

class AccountCreationPolicy extends InstanceResource implements Saveable {

    const WELCOME_EMAIL_STATUS                  = 'welcomeEmailStatus';
    const EMAIL_DOMAIN_WHITELIST                = 'emailDomainWhitelist';
    const EMAIL_DOMAIN_BLACKLIST                = 'emailDomainBlacklist';
    const WELCOME_EMAIL_TEMPLATES               = 'welcomeEmailTemplates';
    const VERIFICATION_EMAIL_STATUS             = 'verificationEmailStatus';
    const VERIFICATION_EMAIL_TEMPLATES          = 'verificationEmailTemplates';
    const VERIFICATION_SUCCESS_EMAIL_STATUS     = 'verificationSuccessEmailStatus';
    const VERIFICATION_SUCCESS_EMAIL_TEMPLATES  = 'verificationSuccessEmailTemplates';

    public function setVerificationEmailStatus($status)
    {
        $this->setProperty(self::VERIFICATION_EMAIL_STATUS, $status);
    }

    public function getVerificationEmailStatus()
    {
        return $this->getProperty(self::VERIFICATION_EMAIL_STATUS);
    }

    public function setWelcomeEmailStatus($status)
    {
        $this->setProperty(self::WELCOME_EMAIL_STATUS, $status);
    }

    public function getWelcomeEmailStatus()
    {
        return $this->getProperty(self::WELCOME_EMAIL_STATUS);
    }

    public function setVerificationSuccessEmailStatus($status)
    {
        $this->setProperty(self::VERIFICATION_SUCCESS_EMAIL_STATUS, $status);
    }

    public function getVerificationSuccessEmailStatus()
    {
        return $this->getProperty(self::VERIFICATION_SUCCESS_EMAIL_STATUS);
    }

    public function getVerificationEmailTemplates(array $options = [])
    {
        return $this->getResourceProperty(self::VERIFICATION_EMAIL_TEMPLATES, Stormpath::MODELED_EMAIL_TEMPLATE_LIST, $options);
    }

    public function getVerificationSuccessEmailTemplates(array $options = [])
    {
        return $this->getResourceProperty(self::VERIFICATION_SUCCESS_EMAIL_TEMPLATES, Stormpath::UNMODELED_EMAIL_TEMPLATE_LIST, $options);
    }

    public function getWelcomeEmailTemplates(array $options = [])
    {
        return $this->getResourceProperty(self::WELCOME_EMAIL_TEMPLATES, Stormpath::UNMODELED_EMAIL_TEMPLATE_LIST, $options);
    }

    /**
     * Gets the emailDomainWhitelist property
     *
     * @return array
     */
    public function getEmailDomainWhitelist()
    {
        return $this->getProperty(self::EMAIL_DOMAIN_WHITELIST);
    }

    public function addEmailDomainWhitelist($domain)
    {
        if(!is_string($domain)) throw new \InvalidArgumentException('Domain must be a string');

        $whitelist = $this->getProperty(self::EMAIL_DOMAIN_WHITELIST);
        $whitelist[] = $domain;
        $this->setEmailDomainWhitelist(array_unique($whitelist));
    }

    public function removeEmailDomainWhitelist($domain)
    {
        $whitelist = $this->getProperty(self::EMAIL_DOMAIN_WHITELIST);

        $key = array_search($domain, $whitelist);
        if($key !== false) {
            unset($whitelist[$key]);
        }

        $this->setEmailDomainWhitelist(array_unique($whitelist));
    }
    
    /**
     * Sets the emailDomainWhitelist property
     *
     * @param  $emailDomainWhitelist The emailDomainWhitelist of the object
     * @return self
     */
    public function setEmailDomainWhitelist($emailDomainWhitelist)
    {
        $this->setProperty(self::EMAIL_DOMAIN_WHITELIST, $emailDomainWhitelist);
        
        return $this; 
    } 
    
    

    /**
     * Gets the emailDomainBlacklist property
     *
     * @return
     */
    public function getEmailDomainBlacklist()
    {
        return $this->getProperty(self::EMAIL_DOMAIN_BLACKLIST);
    }


    public function addEmailDomainBlacklist($domain)
    {
        if(!is_string($domain)) throw new \InvalidArgumentException('Domain must be a string');

        $blacklist = $this->getProperty(self::EMAIL_DOMAIN_BLACKLIST);
        $blacklist[] = $domain;
        $this->setEmailDomainBlacklist(array_unique($blacklist));
    }

    public function removeEmailDomainBlacklist($domain)
    {
        $blacklist = $this->getProperty(self::EMAIL_DOMAIN_BLACKLIST);

        $key = array_search($domain, $blacklist);
        if($key !== false) {
            unset($blacklist[$key]);
        }

        $this->setEmailDomainBlacklist(array_unique($blacklist));
    }

    /**
     * Sets the emailDomainBlacklist property
     *
     * @param  $emailDomainBlacklist The emailDomainBlacklist of the object
     * @return self
     */
    public function setEmailDomainBlacklist($emailDomainBlacklist)
    {
        $this->setProperty(self::EMAIL_DOMAIN_BLACKLIST, $emailDomainBlacklist);

        return $this;
    }




}