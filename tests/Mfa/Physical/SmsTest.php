<?php
/**
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
 *
 */

namespace Stormpath\Tests\Mfa\Physical;

use Stormpath\Mfa\SmsFactor;
use Stormpath\Resource\Account;
use Stormpath\Resource\Directory;
use Stormpath\Stormpath;
use Stormpath\Tests\TestCase;

class SmsTest extends TestCase
{
    /**
     * @var Phone $testable The testable object.
     */
    protected static $testable;

    /**
     * @var array $properties The properties.
     */
    protected static $properties;

    /**
     * @var Account $account The Account object.
     */
    protected static $account;

    /**
     * @var Directory $directory The Directory object.
     */
    protected static $directory;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();


        static::$properties = [
            "href" => "https://api.stormpath.com/v1/factors/15gIQtFukBXmsdL0dl8qZT",
            "type" => "SMS",
            "createdAt" => "2016-09-27T00:41:34.056Z",
            "modifiedAt" => "2016-09-27T00:41:34.808Z",
            "status" => "ENABLED",
            "verificationStatus" => "UNVERIFIED",
            "account" => [
                "href" => "https://api.stormpath.com/v1/accounts/3w9hThWQ3uAT46sMuWY6dw"
            ],
            "challenges" => [
                "href" => "https://api.stormpath.com/v1/factors/15gIQtFukBXmsdL0dl8qZT/challenges"
            ],
            "phone" => [
                "https://api.stormpath.com/v1/phones/15gIQpvppbEoFoF9RVHZxP"
            ],
            "mostRecentChallenge" => [
                "href" => "https://api.stormpath.com/v1/challenges/4BK3IHnxBfLUpdZBAHQtSe"
            ]
        ];

        $class = new \stdClass();

        foreach (static::$properties as $prop => $value) {
            $class->{$prop} = $value;
        }

        self::$testable = new SmsFactor(null, $class);

        self::$account = static::setupNewAccount();

    }
    public static function tearDownAfterClass()
    {
        self::$directory->delete();

        parent::tearDownAfterClass();


    }
    /** @test */
    public function returns_true()
    {
        $this->assertTrue(true);
    }
    

    /**
     * @group physical
     */
    public function adding_sms_factor_with_challenge_flag_send_a_text_message()
    {

        $smsFactor = SmsFactor::instantiate();
        $smsFactor->challenge = 'Validate with ${code}';

        $factor = self::$account->addFactor($smsFactor);

        $this->assertInstanceOf(Stormpath::FACTOR, $factor, 'A factor Resource was not returned');
        $this->assertInstanceOf(Stormpath::SMS_CHALLENGE, $factor->mostRecentChallenge);
        $this->assertEquals(Stormpath::ENABLED, $factor->status);
        $this->assertEquals(Stormpath::UNVERIFIED, $factor->verificationStatus);

//        $factor->delete();


    }

    private static function setupNewAccount(\Stormpath\Resource\Directory $directory = null)
    {
        if( null === $directory) {
            static::$directory = static::createDirectory();
        }

        return static::$directory->createAccount(
            Account::instantiate([
                'givenName' => 'Account Name',
                'middleName' => 'Middle Name',
                'surname' => 'Surname',
                'username' => makeUniqueName('AccountTest smsFactor') . 'username',
                'email' => makeUniqueName('AccountTest smsFactor') .'@testmail.stormpath.com',
                'password' => 'superP4ss!'
            ])
        );
    }

    private static function createDirectory()
    {
        return self::createResource(
            \Stormpath\Resource\Directory::PATH,
            \Stormpath\Resource\Directory::instantiate([
                'name' => makeUniqueName('SMSFactor Directory')
            ]));
    }
}