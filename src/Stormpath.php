<?php

namespace Stormpath;

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

class Stormpath
{
    const ACCESS_TOKEN                                  = 'AccessToken';
    const ACCESS_TOKEN_LIST                             = 'AccessTokenList';
    const ACCOUNT                                       = 'Account';
    const ACCOUNT_CREATION_POLICY                       = "AccountCreationPolicy";
    const ACCOUNT_LIST                                  = 'AccountList';
    const ACCOUNT_STORE                                 = 'AccountStore';
    const ACCOUNT_STORE_MAPPING                         = 'AccountStoreMapping';
    const ACCOUNT_STORE_MAPPING_LIST                    = 'AccountStoreMappingList';
    const API_KEY                                       = 'ApiKey';
    const API_KEY_LIST                                  = 'ApiKeyList';
    const APPLICATION                                   = 'Application';
    const APPLICATION_LIST                              = 'ApplicationList';
    const ASSERTION_CONSUMER_SERVICE_POST_ENDPOINT      = 'AssertionConsumerServicePostEndpoint';
    const ATTRIBUTE_STATEMENT_MAPPING_RULES             = 'Stormpath\Saml\AttributeStatementMappingRules';
    const AUTHENTICATION_RESULT                         = 'AuthenticationResult';
    const BASIC_LOGIN_ATTEMPT                           = 'BasicLoginAttempt';
    const CUSTOM_DATA                                   = 'CustomData';
    const DIRECTORY                                     = 'Directory';
    const DIRECTORY_LIST                                = 'DirectoryList';
    const EMAIL_VERIFICATION_TOKEN                      = 'EmailVerificationToken';
    const FACEBOOK_PROVIDER                             = 'FacebookProvider';
    const FACEBOOK_PROVIDER_DATA                        = "FacebookProviderData";
    const GITHUB_PROVIDER                               = 'GithubProvider';
    const GITHUB_PROVIDER_DATA                          = "GithubProviderData";
    const GOOGLE_PROVIDER                               = 'GoogleProvider';
    const GOOGLE_PROVIDER_DATA                          = "GoogleProviderData";
    const GRANT_AUTHENTICATION_TOKEN                    = "GrantAuthenticationToken";
    const GROUP                                         = 'Group';
    const GROUP_LIST                                    = 'GroupList';
    const GROUP_MEMBERSHIP                              = 'GroupMembership';
    const GROUP_MEMBERSHIP_LIST                         = 'GroupMembershipList';
    const LINKEDIN_PROVIDER                             = 'LinkedInProvider';
    const LINKEDIN_PROVIDER_DATA                        = "LinkedInProviderData";
    const OAUTH_POLICY                                  = 'OauthPolicy';
    const ORGANIZATION                                  = 'Organization';
    const ORGANIZATION_LIST                             = 'OrganizationList';
    const PASSWORD_RESET_TOKEN                          = 'PasswordResetToken';
    const PROVIDER                                      = 'Provider';
    const PROVIDER_ACCOUNT_ACCESS                       = 'ProviderAccountAccess';
    const PROVIDER_ACCOUNT_RESULT                       = 'ProviderAccountResult';
    const PROVIDER_DATA                                 = 'ProviderData';
    const REFRESH_TOKEN                                 = 'RefreshToken';
    const REFRESH_TOKEN_LIST                            = 'RefreshTokenList';
    const SAML_PROVIDER                                 = 'SamlProvider';
    const SAML_PROVIDER_DATA                            = 'SamlProviderData';
    const TENANT                                        = 'Tenant';
    const VERIFICATION_EMAIL                            = 'VerificationEmail';
    const X509_SIGNING_CERT                             = 'X509SigningCert';

    const ENABLED                                       = 'ENABLED';
    const DISABLED                                      = 'DISABLED';
    const UNVERIFIED                                    = 'UNVERIFIED';
    const LOCKED                                        = 'LOCKED';

    const OFFSET                                        = 'offset';
    const LIMIT                                         = 'limit';
    const SIZE                                          = 'size';
    const EXPAND                                        = 'expand';
    const FILTER                                        = 'q';
    const ORDER_BY                                      = 'orderBy';
    const ASCENDING                                     = 'asc';
    const DESCENDING                                    = 'desc';

    const SAUTHC1_AUTHENTICATION_SCHEME                 = 'SAuthc1';
    const BASIC_AUTHENTICATION_SCHEME                   = 'Basic';

    public static $Statuses             = array(self::DISABLED => self::DISABLED,
                                            self::ENABLED => self::ENABLED);

    public static $AccountStatuses      = array(self::DISABLED => self::DISABLED,
                                            self::ENABLED => self::ENABLED,
                                            self::UNVERIFIED => self::UNVERIFIED,
                                            self::LOCKED => self::LOCKED);

    public static $Sorts                = array(self::ASCENDING => self::ASCENDING,
                                            self::DESCENDING => self::DESCENDING);

}