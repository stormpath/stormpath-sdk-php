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

class ChallengeTest extends \Stormpath\Tests\TestCase
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

        self::$testable = new MockChallenge(null, $class);

    }

    /** @test */
    public function constants_are_correct()
    {
        $reflection = new ReflectionClass(\Stormpath\Mfa\Challenge::class);

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


}


class MockChallenge extends \Stormpath\Mfa\Challenge {
    public static function instantiate($properties = null) {

    }

    public function validate($code) {

    }
}