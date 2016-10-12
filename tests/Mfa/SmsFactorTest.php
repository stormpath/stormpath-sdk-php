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

namespace Stormpath\Tests\Mfa;

use ReflectionClass;
use Stormpath\Mfa\SmsFactor;
use Stormpath\Resource\Account;
use Stormpath\Stormpath;
use Stormpath\Tests\TestCase;

class SmsFactorTest extends TestCase
{
    /**
     * @var Phone $testable The testable object.
     */
    protected static $testable;

    /**
     * @var array $properties The properties.
     */
    protected static $properties;

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
    }

    /** @test */
    public function constants_are_correct()
    {
        $reflection = new ReflectionClass(\Stormpath\Mfa\SmsFactor::class);

        $this->assertEquals('12', count($reflection->getConstants()));

        $this->assertEquals('factors', $reflection->getConstant('PATH'));
        $this->assertEquals('type', $reflection->getConstant('TYPE'));
        $this->assertEquals('href', $reflection->getConstant('HREF_PROP_NAME'));
        $this->assertEquals('createdAt', $reflection->getConstant('CREATED_AT'));
        $this->assertEquals('modifiedAt', $reflection->getConstant('MODIFIED_AT'));
        $this->assertEquals('status', $reflection->getConstant('STATUS'));
        $this->assertEquals('verificationStatus', $reflection->getConstant('VERIFICATION_STATUS'));
        $this->assertEquals('account', $reflection->getConstant('ACCOUNT'));
        $this->assertEquals('challenges', $reflection->getConstant('CHALLENGES'));
        $this->assertEquals('phone', $reflection->getConstant('PHONE'));
        $this->assertEquals('challenge', $reflection->getConstant('CHALLENGE'));
    }

    /**
     * @test
     * @group mfa
     * @group paid-tier
     */
    public function adding_sms_factor_to_account()
    {
        $account = $this->setupNewAccount();

        $smsFactor = new SmsFactor();
        $smsFactor->phone = '(888) 391-5282';

        $factor = $account->addFactor($smsFactor);

        $this->assertInstanceOf(Stormpath::FACTOR, $factor, 'A factor Resource was not returned');
        $this->assertNull($factor->mostRecentChallenge);
        $this->assertEquals(Stormpath::ENABLED, $factor->status);
        $this->assertEquals(Stormpath::UNVERIFIED, $factor->verificationStatus);

        $account->directory->delete();
    }
    

    

    private function setupNewAccount(\Stormpath\Resource\Directory $directory = null)
    {
        if( null === $directory) {
            $directory = $this->createDirectory();
        }

        return $directory->createAccount(
            Account::instantiate([
                'givenName' => 'Account Name',
                'middleName' => 'Middle Name',
                'surname' => 'Surname',
                'username' => makeUniqueName('AccountTest smsFactor') . 'username',
                'email' => makeUniqueName('AccountTest smsFactor') .'@mailinator.com',
                'password' => 'superP4ss!'
            ])
        );
    }

    private function createDirectory()
    {
        return self::createResource(
            \Stormpath\Resource\Directory::PATH,
            \Stormpath\Resource\Directory::instantiate([
                'name' => makeUniqueName('SMSFactor Directory')
            ]));
    }


}