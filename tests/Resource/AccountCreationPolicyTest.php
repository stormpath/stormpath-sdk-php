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

    /** @test */
    public function accessor_for_verification_email_templates_returns_modeled_email_template_list_resource()
    {
        $verificationEmailTemplates = self::$acp->getVerificationEmailTemplates();
        $this->assertInstanceOf(\Stormpath\Mail\ModeledEmailTemplateList::class, $verificationEmailTemplates);
    }

    /** @test */
    public function accessor_for_verification_success_email_templates_returns_unmodeled_email_template_list_resource()
    {
        $verificationSuccessEmailTemplates = self::$acp->getVerificationSuccessEmailTemplates();
        $this->assertInstanceOf(\Stormpath\Mail\UnmodeledEmailTemplateList::class, $verificationSuccessEmailTemplates);
    }

    /** @test */
    public function accessor_for_welcome_email_templates_returns_unmodeled_email_template_list_resource()
    {
        $welcomeEmailTemplates = self::$acp->getWelcomeEmailTemplates();
        $this->assertInstanceOf(\Stormpath\Mail\UnmodeledEmailTemplateList::class, $welcomeEmailTemplates);
    }

    /** @test */
    public function email_domain_whitelist_returns_array()
    {
        $emailWhitelist = self::$acp->getEmailDomainWhitelist();
        $this->assertTrue(is_array($emailWhitelist));
    }

    /** @test */
    public function ability_to_set_domain_whitelist()
    {
        $this->assertEmpty(self::$acp->getEmailDomainWhitelist());

        self::$acp->setEmailDomainWhitelist(['abc.com', 'xyz.com']);

        $this->assertCount(2, self::$acp->getEmailDomainWhitelist());

        self::$acp->setEmailDomainWhitelist([]);

        $this->assertCount(0, self::$acp->getEmailDomainWhitelist());

    }


    /** @test */
    public function ability_to_add_domain_whitelist()
    {
        $this->assertEmpty(self::$acp->getEmailDomainWhitelist());

        self::$acp->addEmailDomainWhitelist('gmail.com');
        self::$acp->addEmailDomainWhitelist('stormpath.com');
        self::$acp->addEmailDomainWhitelist('stormpath.com');

        $this->assertCount(2, self::$acp->getEmailDomainWhitelist());

    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function must_pass_string_to_add_domain_whitelist()
    {
        self::$acp->addEmailDomainWhitelist(['gmail.com']);
    }


    /** @test */
    public function ability_to_remove_single_whitelist_domain()
    {
        self::$acp->setEmailDomainWhitelist([]);
        self::$acp->addEmailDomainWhitelist('gmail.com');
        self::$acp->addEmailDomainWhitelist('stormpath.com');
        self::$acp->addEmailDomainWhitelist('xyz.com');
        $this->assertCount(3, self::$acp->getEmailDomainWhitelist());

        self::$acp->removeEmailDomainWhitelist('xyz.com');
        $this->assertCount(2, self::$acp->getEmailDomainWhitelist());

        self::$acp->removeEmailDomainWhitelist('gmail.com');
        $this->assertCount(1, self::$acp->getEmailDomainWhitelist());

        self::$acp->removeEmailDomainWhitelist('blah.com');
        $this->assertCount(1, self::$acp->getEmailDomainWhitelist());

    }



    /** @test */
    public function email_domain_blacklist_returns_array()
    {
        $emailBlacklist = self::$acp->getEmailDomainBlacklist();
        $this->assertTrue(is_array($emailBlacklist));
    }


    /** @test */
    public function ability_to_set_domain_blacklist()
    {
        $this->assertEmpty(self::$acp->getEmailDomainBlacklist());

        self::$acp->setEmailDomainBlacklist(['abc.com', 'xyz.com']);

        $this->assertCount(2, self::$acp->getEmailDomainBlacklist());

        self::$acp->setEmailDomainBlacklist([]);

        $this->assertCount(0, self::$acp->getEmailDomainBlacklist());

    }


    /** @test */
    public function ability_to_add_domain_blacklist()
    {
        $this->assertEmpty(self::$acp->getEmailDomainBlacklist());

        self::$acp->addEmailDomainBlacklist('gmail.com');
        self::$acp->addEmailDomainBlacklist('stormpath.com');
        self::$acp->addEmailDomainBlacklist('stormpath.com');

        $this->assertCount(2, self::$acp->getEmailDomainBlacklist());

    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function must_pass_string_to_add_domain_blacklist()
    {
        self::$acp->addEmailDomainBlacklist(['gmail.com']);
    }


    /** @test */
    public function ability_to_remove_single_blacklist_domain()
    {
        self::$acp->setEmailDomainBlacklist([]);
        self::$acp->addEmailDomainBlacklist('gmail.com');
        self::$acp->addEmailDomainBlacklist('stormpath.com');
        self::$acp->addEmailDomainBlacklist('xyz.com');
        $this->assertCount(3, self::$acp->getEmailDomainBlacklist());

        self::$acp->removeEmailDomainBlacklist('xyz.com');
        $this->assertCount(2, self::$acp->getEmailDomainBlacklist());

        self::$acp->removeEmailDomainBlacklist('gmail.com');
        $this->assertCount(1, self::$acp->getEmailDomainBlacklist());

        self::$acp->removeEmailDomainBlacklist('blah.com');
        $this->assertCount(1, self::$acp->getEmailDomainBlacklist());

    }
    


    public static function tearDownAfterClass()
    {
        self::$directory->delete();
        parent::tearDownAfterClass();
    }
}