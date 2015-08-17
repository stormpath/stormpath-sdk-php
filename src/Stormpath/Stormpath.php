<?php

namespace Stormpath;

/*
 * Copyright 2013 Stormpath, Inc.
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

// @codeCoverageIgnoreStart
use Stormpath\Client;

function Stormpath_autoload($className) {
    if (substr($className, 0, 9) != 'Stormpath') {
        return false;
    }
    $file = str_replace('\\', '/', $className);
    return include dirname(__FILE__) . "$file";
}
// @codeCoverageIgnoreEnd

spl_autoload_register('Stormpath\Stormpath_autoload');

class Stormpath
{
    const ACCOUNT                               = 'Account';
    const ACCOUNT_CREATION_POLICY               = "AccountCreationPolicy";
    const ACCOUNT_LIST                          = 'AccountList';
    const ACCOUNT_STORE                         = 'AccountStore';
    const ACCOUNT_STORE_MAPPING                 = 'AccountStoreMapping';
    const ACCOUNT_STORE_MAPPING_LIST            = 'AccountStoreMappingList';
    const API_KEY                               = 'ApiKey';
    const API_KEY_LIST                          = 'ApiKeyList';
    const APPLICATION                           = 'Application';
    const APPLICATION_LIST                      = 'ApplicationList';
    const AUTHENTICATION_RESULT                 = 'AuthenticationResult';
    const BASIC_LOGIN_ATTEMPT                   = 'BasicLoginAttempt';
    const CUSTOM_DATA                           = 'CustomData';
    const DIRECTORY                             = 'Directory';
    const DIRECTORY_LIST                        = 'DirectoryList';
    const EMAIL_VERIFICATION_TOKEN              = 'EmailVerificationToken';
    const FACEBOOK_PROVIDER                     = 'FacebookProvider';
    const FACEBOOK_PROVIDER_DATA                = "FacebookProviderData";
    const GOOGLE_PROVIDER                       = 'GoogleProvider';
    const GOOGLE_PROVIDER_DATA                  = "GoogleProviderData";
    const GROUP                                 = 'Group';
    const GROUP_LIST                            = 'GroupList';
    const GROUP_MEMBERSHIP                      = 'GroupMembership';
    const GROUP_MEMBERSHIP_LIST                 = 'GroupMembershipList';
    const PASSWORD_RESET_TOKEN                  = 'PasswordResetToken';
    const PROVIDER                              = 'Provider';
    const PROVIDER_ACCOUNT_ACCESS               = 'ProviderAccountAccess';
    const PROVIDER_ACCOUNT_RESULT               = 'ProviderAccountResult';
    const PROVIDER_DATA                         = 'ProviderData';
    const TENANT                                = 'Tenant';
    const VERIFICATION_EMAIL                    = 'VerificationEmail';

    const ENABLED                               = 'ENABLED';
    const DISABLED                              = 'DISABLED';
    const UNVERIFIED                            = 'UNVERIFIED';
    const LOCKED                                = 'LOCKED';

    const OFFSET                                = 'offset';
    const LIMIT                                 = 'limit';
    const SIZE                                  = 'size';
    const EXPAND                                = 'expand';
    const FILTER                                = 'q';
    const ORDER_BY                              = 'orderBy';
    const ASCENDING                             = 'asc';
    const DESCENDING                            = 'desc';

    const AUTHENTICATION_SAUTHC1                = 'SAuthc1';

    public static $Statuses             = array(self::DISABLED => self::DISABLED,
                                            self::ENABLED => self::ENABLED);

    public static $AccountStatuses      = array(self::DISABLED => self::DISABLED,
                                            self::ENABLED => self::ENABLED,
                                            self::UNVERIFIED => self::UNVERIFIED,
                                            self::LOCKED => self::LOCKED);

    public static $Sorts                = array(self::ASCENDING => self::ASCENDING,
                                            self::DESCENDING => self::DESCENDING);

}