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
}