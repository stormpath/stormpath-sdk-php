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