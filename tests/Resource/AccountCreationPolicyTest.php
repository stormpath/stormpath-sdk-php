<?php

namespace Stormpath\Tests\Resource;


use Stormpath\Stormpath;

class AccountCreationPolicyTest extends \Stormpath\Tests\TestCase {

    private static $directory;

    private static $acp;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$directory = \Stormpath\Resource\Directory::create(array('name' => makeUniqueName('AccountCreationPolicyTest')));
        self::$acp = self::$directory->accountCreationPolicy;
    }

    private function enableAndTest($property)
    {
        self::$acp->$property = STORMPATH::ENABLED;
        self::$acp->save();

        $acp = self::$directory->accountCreationPolicy;
        $this->assertEquals(STORMPATH::ENABLED, $acp->$property);
    }

    private function disableAndTest($property)
    {
        self::$acp->$property = STORMPATH::DISABLED;
        self::$acp->save();

        $acp = self::$directory->accountCreationPolicy;
        $this->assertEquals(STORMPATH::DISABLED, $acp->$property);
    }

    /**
     * @test
     */
    public function it_should_allow_changing_verification_email_status()
    {
        $this->assertEquals(STORMPATH::DISABLED, self::$acp->verificationEmailStatus);


        $this->enableAndTest('verificationEmailStatus');
        $this->disableAndTest('verificationEmailStatus');
    }

    /**
     * @test
     */
    public function it_should_allow_changing_verification_success_email_status()
    {
        $this->assertEquals(STORMPATH::DISABLED, self::$acp->verificationSuccessEmailStatus);

        $this->enableAndTest('verificationSuccessEmailStatus');
        $this->disableAndTest('verificationSuccessEmailStatus');
    }

    /**
     * @test
     */
    public function it_should_allow_changing_welcome_email_status()
    {
        $this->assertEquals(STORMPATH::DISABLED, self::$acp->welcomeEmailStatus);

        $this->enableAndTest('welcomeEmailStatus');
        $this->disableAndTest('welcomeEmailStatus');
    }

    public static function tearDownAfterClass()
    {
        self::$directory->delete();
        parent::tearDownAfterClass();
    }
}