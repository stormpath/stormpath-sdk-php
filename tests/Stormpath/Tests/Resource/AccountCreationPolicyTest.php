<?php

namespace Stormpath\Tests\Resource;


use Stormpath\Stormpath;

class AccountCreationPolicyTest extends \Stormpath\Tests\BaseTest {

    private static $directory;

    private static $acp;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$directory = \Stormpath\Resource\Directory::create(array('name' => 'Another random directory' .md5(time().microtime().uniqid())));
        self::$acp = self::$directory->accountCreationPolicy;
    }

    /**
     * @test
     */
    public function it_should_allow_changing_verification_email_status()
    {
        $this->assertEquals(STORMPATH::DISABLED, self::$acp->verificationEmailStatus);

        self::$acp->verificationEmailStatus = STORMPATH::ENABLED;

        $this->assertEquals(STORMPATH::ENABLED, self::$acp->verificationEmailStatus);

        self::$acp->verificationEmailStatus = STORMPATH::DISABLED;

        $this->assertEquals(STORMPATH::DISABLED, self::$acp->verificationEmailStatus);
    }

    /**
     * @test
     */
    public function it_should_allow_changing_verification_success_email_status()
    {
        $this->assertEquals(STORMPATH::DISABLED, self::$acp->verificationSuccessEmailStatus);

        self::$acp->verificationSuccessEmailStatus = STORMPATH::ENABLED;

        $this->assertEquals(STORMPATH::ENABLED, self::$acp->verificationSuccessEmailStatus);

        self::$acp->verificationSuccessEmailStatus = STORMPATH::DISABLED;

        $this->assertEquals(STORMPATH::DISABLED, self::$acp->verificationSuccessEmailStatus);
    }

    /**
     * @test
     */
    public function it_should_allow_changing_welcome_email_status()
    {
        $this->assertEquals(STORMPATH::DISABLED, self::$acp->welcomeEmailStatus);

        self::$acp->welcomeEmailStatus = STORMPATH::ENABLED;

        $this->assertEquals(STORMPATH::ENABLED, self::$acp->welcomeEmailStatus);

        self::$acp->welcomeEmailStatus = STORMPATH::DISABLED;

        $this->assertEquals(STORMPATH::DISABLED, self::$acp->welcomeEmailStatus);
    }

    public static function tearDownAfterClass()
    {
        self::$directory->delete();
        parent::tearDownAfterClass();
    }
}