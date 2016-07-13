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

namespace Stormpath\Directory;

use Stormpath\Client;
use Stormpath\Resource\InstanceResource;
use Stormpath\Stormpath;

class PasswordPolicy extends InstanceResource
{
    const RESET_TOKEN_TTL               = "resetTokenTtl";
    const PASSWORD_STRENGTH             = "strength";
    const RESET_EMAIL_STATUS            = "resetEmailStatus";
    const RESET_EMAIL_TEMPLATES         = "resetEmailTemplates";
    const RESET_SUCCESS_EMAIL_STATUS    = "resetSuccessEmailStatus";
    const RESET_SUCCESS_EMAIL_TEMPLATES = "resetSuccessEmailTemplates";

    const PATH                          = 'passwordPolicies';

    public static function get($href, array $options = [])
    {
        return Client::get($href, Stormpath::PASSWORD_POLICY, self::PATH, $options);
    }

    public function getResetTokenTtl()
    {
        return $this->getProperty(self::RESET_TOKEN_TTL);
    }

    public function setResetTokenTtl($ttl)
    {
        $this->setProperty(self::RESET_TOKEN_TTL, $ttl);
    }

    public function getResetEmailStatus()
    {
        return $this->getProperty(self::RESET_EMAIL_STATUS);
    }

    public function setResetEmailStatus($ttl)
    {
        $this->setProperty(self::RESET_EMAIL_STATUS, $ttl);
    }

    public function getResetSuccessEmailStatus()
    {
        return $this->getProperty(self::RESET_EMAIL_STATUS);
    }

    public function setResetSuccessEmailStatus($ttl)
    {
        $this->setProperty(self::RESET_EMAIL_STATUS, $ttl);
    }

    public function getStrength(array $options = [])
    {
        return $this->getResourceProperty(self::PASSWORD_STRENGTH, Stormpath::PASSWORD_STRENGTH, $options);
    }

    public function getResetEmailTemplates(array $options = [])
    {
        return $this->getResourceProperty(self::RESET_EMAIL_TEMPLATES, Stormpath::MODELED_EMAIL_TEMPLATE_LIST, $options);
    }

    public function getResetSuccessEmailTemplates(array $options = [])
    {
        return $this->getResourceProperty(self::RESET_SUCCESS_EMAIL_TEMPLATES, Stormpath::UNMODELED_EMAIL_TEMPLATE_LIST, $options);
    }


}