<?php

/*
 * Copyright 2012 Stormpath, Inc.
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

function Services_Stormpath_autoload($className) {
    if (substr($className, 0, 18) != 'Services_Stormpath') {
        return false;
    }
    $file = str_replace('_', '/', $className);
    $file = str_replace('Services/', '', $file);
    return include dirname(__FILE__) . "/$file.php";
}

spl_autoload_register('Services_Stormpath_autoload');

class Services_Stormpath
{
    const ACCOUNT                  = 'Account';
    const ACCOUNT_LIST             = 'AccountList';
    const APPLICATION              = 'Application';
    const APPLICATION_LIST         = 'ApplicationList';
    const AUTHENTICATION_RESULT    = 'Services_Stormpath_Authc_AuthenticationResult';
    const BASIC_LOGIN_ATTEMPT      = 'Services_Stormpath_Authc_BasicLoginAttempt';
    const DIRECTORY                = 'Directory';
    const DIRECTORY_LIST           = 'DirectoryList';
    const EMAIL_VERIFICATION_TOKEN = 'EmailVerificationToken';
    const GROUP                    = 'Group';
    const GROUP_LIST               = 'GroupList';
    const GROUP_MEMBERSHIP         = 'GroupMembership';
    const GROUP_MEMBERSHIP_LIST    = 'GroupMembershipList';
    const PASSWORD_RESET_TOKEN     = 'PasswordResetToken';
    const TENANT                   = 'Tenant';

    const ENABLED                  = 'ENABLED';
    const DISABLED                 = 'DISABLED';

    public static $Statuses        = array(self::DISABLED => self::DISABLED,
                                           self::ENABLED => self::ENABLED);

    public static function createClient($accessId, $secretKey, $baseUrl = null)
    {
        $apiKey = new Services_Stormpath_Client_ApiKey($accessId, $secretKey);

        return new Services_Stormpath_Client_Client($apiKey, $baseUrl);
    }

}