<?php
/**
 * Copyright 2017 Stormpath, Inc.
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
use Stormpath\Mfa\SmsChallenge;
use Stormpath\Stormpath;
use Stormpath\Tests\TestCase;

class SmsChallengeTest extends TestCase
{
    /**
     * @var \Stormpath\Mfa\Challenge $testable The Challenge object.
     */
    private static $testable;

    /**
     * @var array $properties The initial properties.
     */
    private static $properties;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$properties = [
            "href" => "https://api.stormpath.com/v1/challenges/15gIQwZzelqlVSQrq107BX",
            "createdAt" => "2016-09-27T00:41:34.056Z",
            "modifiedAt" => "2016-09-27T00:41:34.808Z",
            "status" => "WAITING_FOR_VALIDATION",
            "message" => "Your verification code is \${code}",
            "account" => [
                "href" => "https://api.stormpath.com/v1/accounts/3w9hThWQ3uAT46sMuWY6dw"
            ],
            "factor" => [
                "href" => "https://api.stormpath.com/v1/factors/15gIQtFukBXmsdL0dl8qZT"
            ]
        ];

        $class = new \stdClass();

        foreach(static::$properties as $prop=>$value)
        {
            $class->{$prop} = $value;
        }

        self::$testable = new \Stormpath\Mfa\SmsChallenge(null, $class);

    }

    /** @test */
    public function constants_are_correct()
    {
        $reflection = new ReflectionClass(\Stormpath\Mfa\SmsChallenge::class);

        $this->assertEquals('9', count($reflection->getConstants()));

        $this->assertEquals('challenges', $reflection->getConstant('PATH'));

        $this->assertEquals('href', $reflection->getConstant('HREF_PROP_NAME'));
        $this->assertEquals('createdAt', $reflection->getConstant('CREATED_AT'));
        $this->assertEquals('modifiedAt', $reflection->getConstant('MODIFIED_AT'));
        $this->assertEquals('status', $reflection->getConstant('STATUS'));
        $this->assertEquals('factor', $reflection->getConstant('FACTOR'));
        $this->assertEquals('account', $reflection->getConstant('ACCOUNT'));
        $this->assertEquals('code', $reflection->getConstant('CODE'));
        $this->assertEquals('message', $reflection->getConstant('MESSAGE'));
    }
    
    /** @test */
    public function message_is_accessible()
    {
        $this->assertEquals(static::$properties['message'], static::$testable->getMessage());
        $this->assertEquals(static::$properties['message'], static::$testable->message);
    }
    
    /** @test */
    public function message_is_settable()
    {
        static::$testable->setMessage('message 1 ' . Stormpath::MFA_CHALLENGE_CODE_PLACEHOLDER);
        static::assertEquals('message 1 ' . Stormpath::MFA_CHALLENGE_CODE_PLACEHOLDER, static::$testable->getMessage());
    
        static::$testable->message = 'message 2 ' . Stormpath::MFA_CHALLENGE_CODE_PLACEHOLDER;
        static::assertEquals('message 2 ' . Stormpath::MFA_CHALLENGE_CODE_PLACEHOLDER, static::$testable->getMessage());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function a_message_must_contain_the_code_placeholder()
    {
        static::$testable->setMessage('Something Else');
    }

}