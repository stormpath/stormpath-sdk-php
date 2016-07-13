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
namespace Stormpath\Tests\Mail;

use ReflectionClass;
use Stormpath\Mail\UnmodeledEmailTemplate;
use Stormpath\Mail\UnmodeledEmailTemplateList;
use Stormpath\Stormpath;
use Stormpath\Tests\TestCase;

class ModeledEmailTemplateTest extends TestCase
{
    private static $modeledEmailTemplate;
    private static $properties;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$properties = [
            'href' => 'https://api.stormpath.com/emailTemplates/2PCjpMa5kihBOo1eO8L6z5',
            'name' => 'My Email',
            'description' => 'My Description',
            'fromName' => 'John Doe',
            'fromEmailAddress' => 'john.doe@email.com',
            'subject' => 'Your Password has been changed!',
            'textBody' => 'Your password has been successfully changed',
            'htmlBody' => 'Your password has been <b>successfully</b> changed',
            'mimeType' => Stormpath::MIME_PLAIN_TEXT
        ];

        $class = new \stdClass();

        foreach(static::$properties as $prop=>$value)
        {
            $class->{$prop} = $value;
        }

        self::$modeledEmailTemplate = new UnmodeledEmailTemplate(null, $class);

    }

    /** @test */
    public function constants_are_correct()
    {
        $reflection = new ReflectionClass(UnmodeledEmailTemplate::class);

        $this->assertEquals('10', count($reflection->getConstants()));

        $this->assertEquals('href', $reflection->getConstant('HREF_PROP_NAME'));
        $this->assertEquals('name', $reflection->getConstant('NAME'));
        $this->assertEquals('description', $reflection->getConstant('DESCRIPTION'));
        $this->assertEquals('fromName', $reflection->getConstant('FROM_NAME'));
        $this->assertEquals('fromEmailAddress', $reflection->getConstant('FROM_EMAIL_ADDRESS'));
        $this->assertEquals('subject', $reflection->getConstant('SUBJECT'));
        $this->assertEquals('textBody', $reflection->getConstant('TEXT_BODY'));
        $this->assertEquals('htmlBody', $reflection->getConstant('HTML_BODY'));
        $this->assertEquals('mimeType', $reflection->getConstant('MIME_TYPE'));
        $this->assertEquals('emailTemplates', $reflection->getConstant('PATH'));
    }

    /** @test */
    public function href_is_accessible()
    {
        $this->assertEquals(static::$properties['href'], static::$modeledEmailTemplate->getHref());
        $this->assertEquals(static::$properties['href'], static::$modeledEmailTemplate->href);

    }

    /** @test */
    public function name_is_accessible()
    {
        $this->assertEquals(static::$properties['name'], static::$modeledEmailTemplate->getName());
        $this->assertEquals(static::$properties['name'], static::$modeledEmailTemplate->name);
    }

    /** @test */
    public function name_is_settable()
    {
        static::$modeledEmailTemplate->setName('New Email Name');
        $this->assertEquals('New Email Name', static::$modeledEmailTemplate->getName());

        static::$modeledEmailTemplate->name = 'Email Name';
        $this->assertEquals('Email Name', static::$modeledEmailTemplate->getName());
    }


    /** @test */
    public function description_is_accessible()
    {
        $this->assertEquals(static::$properties['description'], static::$modeledEmailTemplate->getDescription());
        $this->assertEquals(static::$properties['description'], static::$modeledEmailTemplate->description);
    }

    /** @test */
    public function description_is_settable()
    {
        static::$modeledEmailTemplate->setDescription('My New Description');
        $this->assertEquals('My New Description', static::$modeledEmailTemplate->getDescription());

        static::$modeledEmailTemplate->description = 'My Description';
        $this->assertEquals('My Description', static::$modeledEmailTemplate->getDescription());
    }

    /** @test */
    public function from_name_is_accessible()
    {
        $this->assertEquals(static::$properties['fromName'], static::$modeledEmailTemplate->getFromName());
        $this->assertEquals(static::$properties['fromName'], static::$modeledEmailTemplate->fromName);
    }

    /** @test */
    public function from_name_is_settable()
    {
        static::$modeledEmailTemplate->setFromName('John Doe Jr.');
        $this->assertEquals('John Doe Jr.', static::$modeledEmailTemplate->getFromName());

        static::$modeledEmailTemplate->fromName = 'John Doe';
        $this->assertEquals('John Doe', static::$modeledEmailTemplate->getFromName());
    }

    /** @test */
    public function from_email_address_is_accessible()
    {
        $this->assertEquals(static::$properties['fromEmailAddress'], static::$modeledEmailTemplate->getFromEmailAddress());
        $this->assertEquals(static::$properties['fromEmailAddress'], static::$modeledEmailTemplate->fromEmailAddress);
    }

    /** @test */
    public function from_email_address_is_settable()
    {
        static::$modeledEmailTemplate->setFromEmailAddress('john.doe.jr@example.com');
        $this->assertEquals('john.doe.jr@example.com', static::$modeledEmailTemplate->getFromEmailAddress());

        static::$modeledEmailTemplate->fromEmailAddress = 'john.doe@example.com';
        $this->assertEquals('john.doe@example.com', static::$modeledEmailTemplate->getFromEmailAddress());
    }

    /** @test */
    public function subject_is_accessible()
    {
        $this->assertEquals(static::$properties['subject'], static::$modeledEmailTemplate->getSubject());
        $this->assertEquals(static::$properties['subject'], static::$modeledEmailTemplate->subject);
    }

    /** @test */
    public function subject_is_settable()
    {
        static::$modeledEmailTemplate->setSubject('Your password has been reset');
        $this->assertEquals('Your password has been reset', static::$modeledEmailTemplate->getSubject());

        static::$modeledEmailTemplate->subject = 'Your Password has been changed!';
        $this->assertEquals('Your Password has been changed!', static::$modeledEmailTemplate->getSubject());
    }

    /** @test */
    public function text_body_is_accessible()
    {
        $this->assertEquals(static::$properties['textBody'], static::$modeledEmailTemplate->getTextBody());
        $this->assertEquals(static::$properties['textBody'], static::$modeledEmailTemplate->textBody);
    }

    /** @test */
    public function text_body_is_settable()
    {
        static::$modeledEmailTemplate->setTextBody('Your password has been successfully reset');
        $this->assertEquals('Your password has been successfully reset', static::$modeledEmailTemplate->getTextBody());

        static::$modeledEmailTemplate->textBody = 'Your password has been successfully changed';
        $this->assertEquals('Your password has been successfully changed', static::$modeledEmailTemplate->getTextBody());
    }

    /** @test */
    public function html_body_is_accessible()
    {
        $this->assertEquals(static::$properties['htmlBody'], static::$modeledEmailTemplate->getHtmlBody());
        $this->assertEquals(static::$properties['htmlBody'], static::$modeledEmailTemplate->htmlBody);
    }

    /** @test */
    public function html_body_is_settable()
    {
        static::$modeledEmailTemplate->setHtmlBody('Your password has been <b>successfully</b> reset.');
        $this->assertEquals('Your password has been <b>successfully</b> reset.', static::$modeledEmailTemplate->getHtmlBody());

        static::$modeledEmailTemplate->htmlBody = 'Your password has been <b>successfully</b> changed';
        $this->assertEquals('Your password has been <b>successfully</b> changed', static::$modeledEmailTemplate->getHtmlBody());
    }

    /** @test */
    public function mime_type_is_accessible()
    {
        $this->assertEquals(static::$properties['mimeType'], static::$modeledEmailTemplate->getMimeType());
        $this->assertEquals(static::$properties['mimeType'], static::$modeledEmailTemplate->mimeType);
    }

    /** @test */
    public function mime_type_is_settable()
    {
        static::$modeledEmailTemplate->setMimeType(Stormpath::MIME_HTML);
        $this->assertEquals(Stormpath::MIME_HTML, static::$modeledEmailTemplate->getMimeType());

        static::$modeledEmailTemplate->mimeType = Stormpath::MIME_PLAIN_TEXT;
        $this->assertEquals(Stormpath::MIME_PLAIN_TEXT, static::$modeledEmailTemplate->getMimeType());
    }



    /** @test */
    public function the_email_template_can_be_saved()
    {
        $directory = \Stormpath\Resource\Directory::instantiate(array('name' => makeUniqueName('ModeledEmailTemplate'), 'description' => 'Main Directory description'));
        self::createResource(\Stormpath\Resource\Directory::PATH, $directory);

        foreach($directory->passwordPolicy->getResetSuccessEmailTemplates() as $emailTemplate) {
            $emailTemplate->fromName = 'John Doe';
            $emailTemplate->save();
        }



        foreach($directory->passwordPolicy->getResetSuccessEmailTemplates() as $emailTemplate) {
            $this->assertEquals('John Doe', $emailTemplate->fromName);
        }

        $directory->delete();
    }









}