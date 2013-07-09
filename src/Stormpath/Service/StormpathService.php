<?php

namespace Stormpath\Service;

use Stormpath\Client\ApiKey;
use Stormpath\Client\Client;

function autoload($className) {
    if (substr($className, 0, 18) != 'Services_Stormpath') {
        return false;
    }
    $file = str_replace('_', '/', $className);
    $file = str_replace('Services/', '', $file);
    return include dirname(__FILE__) . "/$file.php";
}

spl_autoload_register('autoload');

class StormpathService
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
        $apiKey = new ApiKey($accessId, $secretKey);

        return new Client($apiKey, $baseUrl);
    }

}
