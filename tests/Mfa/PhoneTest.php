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
use Stormpath\Mfa\Phone;
use Stormpath\Resource\Account;
use Stormpath\Resource\Directory;
use Stormpath\Stormpath;
use Stormpath\Tests\TestCase;

class PhoneTest extends TestCase
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
            "href" => "https://api.stormpath.com/v1/phones/15gIQpvppbEoFoF9RVHZxP",
            "createdAt" => "2016-09-27T00:41:34.056Z",
            "modifiedAt" => "2016-09-27T00:41:34.808Z",
            "status" => "ENABLED",
            "number" => "+15133608855",
            "description" => "Description of phone number",
            "name" => "Name of the phone",
            "verificationStatus" => "UNVERIFIED",
            "account" => [
                "href" => "https://api.stormpath.com/v1/accounts/3w9hThWQ3uAT46sMuWY6dw"
            ]
        ];

        $class = new \stdClass();

        foreach(static::$properties as $prop=>$value)
        {
            $class->{$prop} = $value;
        }

        self::$testable = new \Stormpath\Mfa\Phone(null, $class);
    }

    /** @test */
    public function constants_are_correct()
    {
        $reflection = new ReflectionClass(\Stormpath\Mfa\Phone::class);

        $this->assertEquals('10', count($reflection->getConstants()));

        $this->assertEquals('phones', $reflection->getConstant('PATH'));
        $this->assertEquals('href', $reflection->getConstant('HREF_PROP_NAME'));
        $this->assertEquals('createdAt', $reflection->getConstant('CREATED_AT'));
        $this->assertEquals('modifiedAt', $reflection->getConstant('MODIFIED_AT'));
        $this->assertEquals('status', $reflection->getConstant('STATUS'));
        $this->assertEquals('verificationStatus', $reflection->getConstant('VERIFICATION_STATUS'));
        $this->assertEquals('account', $reflection->getConstant('ACCOUNT'));
        $this->assertEquals('number', $reflection->getConstant('PHONE_NUMBER'));
        $this->assertEquals('description', $reflection->getConstant('DESCRIPTION'));
        $this->assertEquals('name', $reflection->getConstant('NAME'));
    }

    /** @test */
    public function href_is_accessible()
    {
        $this->assertEquals(static::$properties['href'], static::$testable->getHref());
        $this->assertEquals(static::$properties['href'], static::$testable->href);
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

    /** @test */
    public function number_is_accessible()
    {
        $this->assertEquals(static::$properties['number'], static::$testable->getNumber());
        $this->assertEquals(static::$properties['number'], static::$testable->number);
        $this->assertEquals(static::$properties['number'], static::$testable->getPhoneNumber());
        $this->assertEquals(static::$properties['number'], static::$testable->phoneNumber);
    }

    /** @test */
    public function number_is_settable()
    {
        static::$testable->setNumber(5555551212);
        $this->assertEquals(5555551212, static::$testable->getNumber());

        static::$testable->number = 6666661212;
        $this->assertEquals(6666661212, static::$testable->getNumber());

        static::$testable->setPhoneNumber(5555551212);
        $this->assertEquals(5555551212, static::$testable->getPhoneNumber());

        static::$testable->phoneNumber = 6666661212;
        $this->assertEquals(6666661212, static::$testable->getPhoneNumber());
    }

    /** @test */
    public function description_is_accessible()
    {
        $this->assertEquals(static::$properties['description'], static::$testable->getDescription());
        $this->assertEquals(static::$properties['description'], static::$testable->description);
    }
    
    /** @test */
    public function description_is_settable()
    {
        static::$testable->setDescription('Test Description');
        static::assertEquals('Test Description', static::$testable->getDescription());
    
        static::$testable->description = 'Another Test Description';
        static::assertEquals('Another Test Description', static::$testable->getDescription());
    }
    
    

    /** @test */
    public function name_is_accessible()
    {
        $this->assertEquals(static::$properties['name'], static::$testable->getName());
        $this->assertEquals(static::$properties['name'], static::$testable->name);
    }
    
    /** @test */
    public function name_is_settable()
    {
        static::$testable->setName('Phone Name');
        static::assertEquals('Phone Name', static::$testable->getName());
    
        static::$testable->name = 'Test Phone Name';
        static::assertEquals('Test Phone Name', static::$testable->getName());
    }

    /** @test */
    public function a_phone_can_be_instantiated()
    {
        /** @var Phone $phone */
        $phone = null;
        $phoneProperties = [
            'name' => 'Test Phone',
            'description' => 'Test Phone Description',
            'number' => '5555551212'
        ];

        $phone = Phone::instantiate(
            $phoneProperties
        );

        self::assertInstanceOf(
            \Stormpath\Mfa\Phone::class,
            $phone,
            'The returned object from instantiate was not an instance of Stormpath\Mfa\Phone'
        );

        self::assertEquals($phoneProperties['number'], $phone->getNumber());
        self::assertEquals($phoneProperties['name'], $phone->getName());
        self::assertEquals($phoneProperties['description'], $phone->getDescription());
    }

}