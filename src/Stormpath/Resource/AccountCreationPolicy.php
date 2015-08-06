<?php
namespace Stormpath\Resource;

class AccountCreationPolicy extends InstanceResource implements Saveable {

    const VERIFICATION_EMAIL_STATUS             = 'verificationEmailStatus';
    const WELCOME_EMAIL_STATUS                  = 'welcomeEmailStatus';
    const VERIFICATION_SUCCESS_EMAIL_STATUS     = 'verificationSuccessEmailStatus';

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
}