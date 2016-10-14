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
use Stormpath\Mfa\GoogleAuthenticatorFactor;
use Stormpath\Mfa\SmsFactor;
use Stormpath\Resource\Account;
use Stormpath\Stormpath;
use Stormpath\Tests\TestCase;

class GoogleAuthenticatorFactorTest extends TestCase
{

    /**
     * @var GoogleAuthenticatorFactor $testable The Google Authenticator Factor.
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
            "type" => "google-authenticator",
            "createdAt" => "2016-09-27T00:41:34.056Z",
            "modifiedAt" => "2016-09-27T00:41:34.808Z",
            "status" => "ENABLED",
            "verificationStatus" => "UNVERIFIED",
            "accountName" => "",
            "issuer" => "",
            "secret" => "JBSWY3DPEHPK3PXP",
            "keyUri" => "otpauth://totp/Example:alice@google.com?secret=JBSWY3DPEHPK3PXP&issuer=Example", //?
            "base64QRImage" => "qrcode",
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

        foreach (static::$properties as $prop => $value) {
            $class->{$prop} = $value;
        }

        self::$testable = new GoogleAuthenticatorFactor(null, $class);
    }

    /** @test */
    public function constants_are_correct()
    {
        $reflection = new ReflectionClass(\Stormpath\Mfa\GoogleAuthenticatorFactor::class);

        $this->assertEquals('15', count($reflection->getConstants()));

        $this->assertEquals('factors', $reflection->getConstant('PATH'));
        $this->assertEquals('type', $reflection->getConstant('TYPE'));
        $this->assertEquals('href', $reflection->getConstant('HREF_PROP_NAME'));
        $this->assertEquals('createdAt', $reflection->getConstant('CREATED_AT'));
        $this->assertEquals('modifiedAt', $reflection->getConstant('MODIFIED_AT'));
        $this->assertEquals('status', $reflection->getConstant('STATUS'));
        $this->assertEquals('verificationStatus', $reflection->getConstant('VERIFICATION_STATUS'));
        $this->assertEquals('account', $reflection->getConstant('ACCOUNT'));
        $this->assertEquals('challenges', $reflection->getConstant('CHALLENGES'));
        $this->assertEquals('issuer', $reflection->getConstant('ISSUER'));
        $this->assertEquals('secret', $reflection->getConstant('SECRET'));
        $this->assertEquals('keyUri', $reflection->getConstant('OTP_KEY_URI'));
        $this->assertEquals('accountName', $reflection->getConstant('ACCOUNT_NAME'));
        $this->assertEquals('base64QRImage', $reflection->getConstant('BASE_64_QR_IMAGE'));
    }

    /** @test */
    public function issuer_is_accessible()
    {
        $this->assertEquals(static::$properties['issuer'], static::$testable->getIssuer());
        $this->assertEquals(static::$properties['issuer'], static::$testable->issuer);
    }

    /** @test */
    public function issuer_is_settable()
    {
        static::$testable->setIssuer('issuer');
        static::assertEquals('issuer', static::$testable->getIssuer());

        static::$testable->issuer = 'issuer2';
        static::assertEquals('issuer2', static::$testable->getIssuer());
    }

    /** @test */
    public function secret_is_accessible()
    {
        $this->assertEquals(static::$properties['secret'], static::$testable->getSecret());
        $this->assertEquals(static::$properties['secret'], static::$testable->secret);
    }

    /** @test */
    public function key_uri_is_accessible()
    {
        $this->assertEquals(static::$properties['keyUri'], static::$testable->getKeyUri());
        $this->assertEquals(static::$properties['keyUri'], static::$testable->keyUri);
    }

    /** @test */
    public function account_name_is_accessible()
    {
        $this->assertEquals(static::$properties['accountName'], static::$testable->getAccountName());
        $this->assertEquals(static::$properties['accountName'], static::$testable->accountName);
    }

    /** @test */
    public function account_name_is_settable()
    {
        static::$testable->setAccountName('support@stormpath.com');
        static::assertEquals('support@stormpath.com', static::$testable->getAccountName());

        static::$testable->accountName = 'php@stormpath.com';
        static::assertEquals('php@stormpath.com', static::$testable->getAccountName());
    }


    /** @test */
    public function base_64_qr_image_is_accessible()
    {
        $this->assertEquals(static::$properties['base64QRImage'], static::$testable->getBase64QRImage());
        $this->assertEquals(static::$properties['base64QRImage'], static::$testable->base64QRImage);
    }


    /** @test */
    public function a_google_authenticator_factor_can_be_added_to_an_account()
    {
        $account = $this->setupNewAccount();

        $gaFactor = new GoogleAuthenticatorFactor();
        $gaFactor->accountName = 'brian@stormpath.com';
        $gaFactor->issuer = 'php-test';
        $gaFactor->status = Stormpath::ENABLED;

        /** @var GoogleAuthenticatorFactor $factor */
        $factor = $account->addFactor($gaFactor);

        $this->assertInstanceOf(Stormpath::GOOGLE_AUTHENTICATOR_FACTOR, $factor, 'A factor Resource was not returned');
        $this->assertNull($factor->mostRecentChallenge);
        $this->assertEquals(Stormpath::ENABLED, $factor->status);
        $this->assertEquals(Stormpath::UNVERIFIED, $factor->verificationStatus);
        $this->assertEquals('brian@stormpath.com', $factor->accountName);
        $this->assertEquals('php-test', $factor->issuer);

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