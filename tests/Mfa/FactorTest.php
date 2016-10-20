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

use PHPUnit_Framework_Assert;
use ReflectionClass;
use Stormpath\Mfa\Factor;
use Stormpath\Mfa\SmsFactor;
use Stormpath\Resource\Account;
use Stormpath\Resource\Resource;
use Stormpath\Stormpath;
use Stormpath\Tests\TestCase;

class FactorTest extends TestCase
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
            "type" => "mock",
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
            "mostRecentChallenge" => [
                "href" => "https://api.stormpath.com/v1/challenges/4BK3IHnxBfLUpdZBAHQtSe"
            ]
        ];

        $class = new \stdClass();

        foreach(static::$properties as $prop=>$value)
        {
            $class->{$prop} = $value;
        }

        self::$testable = new TestFactor(null, $class);
    }

    /** @test */
    public function constants_are_correct()
    {
        $reflection = new ReflectionClass(\Stormpath\Mfa\Factor::class);
        
        $this->assertEquals('10', count($reflection->getConstants()));

        $this->assertEquals('factors', $reflection->getConstant('PATH'));
        $this->assertEquals('type', $reflection->getConstant('TYPE'));
        $this->assertEquals('href', $reflection->getConstant('HREF_PROP_NAME'));
        $this->assertEquals('createdAt', $reflection->getConstant('CREATED_AT'));
        $this->assertEquals('modifiedAt', $reflection->getConstant('MODIFIED_AT'));
        $this->assertEquals('status', $reflection->getConstant('STATUS'));
        $this->assertEquals('verificationStatus', $reflection->getConstant('VERIFICATION_STATUS'));
        $this->assertEquals('account', $reflection->getConstant('ACCOUNT'));
        $this->assertEquals('challenges', $reflection->getConstant('CHALLENGES'));
    }
    
    /** @test */
    public function type_is_accessible()
    {
        $this->assertEquals(static::$properties['type'], static::$testable->getType());
        $this->assertEquals(static::$properties['type'], static::$testable->type);
    }

    /** @test */
    public function created_at_is_accessible()
    {
        $this->assertEquals(static::$properties['createdAt'], static::$testable->getCreatedAt());
        $this->assertEquals(static::$properties['createdAt'], static::$testable->createdAt);
    }

    /** @test */
    public function modified_at_is_accessible()
    {
        $this->assertEquals(static::$properties['modifiedAt'], static::$testable->getModifiedAt());
        $this->assertEquals(static::$properties['modifiedAt'], static::$testable->modifiedAt);
    }

    /** @test */
    public function status_is_accessible()
    {
        $this->assertEquals(static::$properties['status'], static::$testable->getStatus());
        $this->assertEquals(static::$properties['status'], static::$testable->status);
    }

    /** @test */
    public function verification_status_is_accessible()
    {
        $this->assertEquals(static::$properties['verificationStatus'], static::$testable->getVerificationStatus());
        $this->assertEquals(static::$properties['verificationStatus'], static::$testable->verificationStatus);
    }


}

class TestFactor extends Factor {
    public function getMostRecentChallenge(array $options = []) {

    }
}