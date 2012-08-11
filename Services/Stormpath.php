<?php

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
    const DIRECTORY                = 'Directory';
    const DIRECTORY_LIST           = 'DirectoryList';
    const EMAIL_VERIFICATION_TOKEN = 'EmailVerificationToken';
    const PASSWORD_RESET_TOKEN     = 'PasswordResetToken';
    const TENANT                   = 'Tenant';

    const ENABLED                  = 'ENABLED';
    const DISABLED                 = 'DISABLED';

    public static $Statuses        = array(self::DISABLED => self::DISABLED,
                                           self::ENABLED => self::ENABLED);

}