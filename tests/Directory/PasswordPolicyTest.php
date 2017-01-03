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

namespace Stormpath\Tests\Directory;

use Stormpath\Stormpath;

class PasswordPolicyTest extends \Stormpath\Tests\TestCase
{
    private static $directory;
    private static $passwordPolicy;
    private static $inited;

    protected static function init()
    {
        self::$directory = \Stormpath\Resource\Directory::instantiate(array('name' => makeUniqueName('PasswordPolicyTest'), 'description' => 'Main Directory description'));
        self::createResource(\Stormpath\Resource\Directory::PATH, self::$directory);
        self::$passwordPolicy = \Stormpath\Directory\PasswordPolicy::get(self::$directory->passwordPolicy->href);
        self::$inited = true;
    }

    public function setUp()
    {
        if (!self::$inited)
        {
            self::init();
        }
    }

    public static function tearDownAfterClass()
    {
        if (self::$directory)
        {
            self::$directory->delete();
        }
        parent::tearDownAfterClass();
    }
    
    /** @test */
    public function can_get_password_policy_based_on_href()
    {
        $passwordPolicy = \Stormpath\Directory\PasswordPolicy::get(self::$directory->passwordPolicy->href);

        $this->assertInstanceOf(\Stormpath\Directory\PasswordPolicy::class, $passwordPolicy);
    }

    /** @test */
    public function reset_token_ttl_accessible()
    {
        $this->assertEquals(24, self::$passwordPolicy->getResetTokenTtl());
        $this->assertEquals(24, self::$passwordPolicy->resetTokenTtl);
    }

    /** @test */
    public function reset_token_ttl_savable()
    {
        self::$passwordPolicy->resetTokenTtl = 10;
        self::$passwordPolicy->save();
        $policy = \Stormpath\Directory\PasswordPolicy::get(self::$directory->passwordPolicy->href);
        $this->assertEquals(10, $policy->resetTokenTtl);

        self::$passwordPolicy->setResetTokenTtl(24);
        self::$passwordPolicy->save();
        $policy = \Stormpath\Directory\PasswordPolicy::get(self::$directory->passwordPolicy->href);
        $this->assertEquals(24, $policy->resetTokenTtl);

    }

    /** @test */
    public function reset_email_status_accessible()
    {
        $this->assertEquals(Stormpath::ENABLED, self::$passwordPolicy->getResetEmailStatus());
        $this->assertEquals(Stormpath::ENABLED, self::$passwordPolicy->resetEmailStatus);
    }

    /** @test */
    public function reset_email_status_savable()
    {
        self::$passwordPolicy->resetEmailStatus = Stormpath::DISABLED;
        self::$passwordPolicy->save();
        $policy = \Stormpath\Directory\PasswordPolicy::get(self::$directory->passwordPolicy->href);
        $this->assertEquals(Stormpath::DISABLED, $policy->resetEmailStatus);

        self::$passwordPolicy->setResetEmailStatus(Stormpath::ENABLED);
        self::$passwordPolicy->save();
        $policy = \Stormpath\Directory\PasswordPolicy::get(self::$directory->passwordPolicy->href);
        $this->assertEquals(Stormpath::ENABLED, $policy->resetEmailStatus);

    }

    /** @test */
    public function reset_success_email_status_accessible()
    {
        $this->assertEquals(Stormpath::ENABLED, self::$passwordPolicy->getResetSuccessEmailStatus());
        $this->assertEquals(Stormpath::ENABLED, self::$passwordPolicy->resetSuccessEmailStatus);
    }

    /** @test */
    public function reset_success_email_status_savable()
    {
        self::$passwordPolicy->resetSuccessEmailStatus = Stormpath::DISABLED;
        self::$passwordPolicy->save();
        $policy = \Stormpath\Directory\PasswordPolicy::get(self::$directory->passwordPolicy->href);
        $this->assertEquals(Stormpath::DISABLED, $policy->resetSuccessEmailStatus);

        self::$passwordPolicy->setResetSuccessEmailStatus(Stormpath::ENABLED);
        self::$passwordPolicy->save();
        $policy = \Stormpath\Directory\PasswordPolicy::get(self::$directory->passwordPolicy->href);
        $this->assertEquals(Stormpath::ENABLED, $policy->resetSuccessEmailStatus);

    }
    
    /** @test */
    public function accessor_for_strength_returns_strength_resource()
    {
        $strength = self::$passwordPolicy->getStrength();
        $this->assertInstanceOf(\Stormpath\Directory\PasswordStrength::class, $strength);
    }
    
    /** @test */
    public function accessor_for_reset_email_templates_returns_modeled_email_template_list_resource()
    {
        $resetEmailTemplates = self::$passwordPolicy->getResetEmailTemplates();
        $this->assertInstanceOf(\Stormpath\Mail\ModeledEmailTemplateList::class, $resetEmailTemplates);
    }

    /** @test */
    public function accessor_for_reset_success_email_templates_returns_unmodeled_email_template_list_resource()
    {
        $resetSuccessEmailTemplates = self::$passwordPolicy->getResetSuccessEmailTemplates();
        $this->assertInstanceOf(\Stormpath\Mail\UnmodeledEmailTemplateList::class, $resetSuccessEmailTemplates);
    }




    
    
    
}