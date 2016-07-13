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

namespace Stormpath\Tests\Directory;

use Stormpath\Stormpath;

class PasswordStrengthTest extends \Stormpath\Tests\TestCase
{
    private static $directory;
    private static $passwordStrength;
    private static $inited;

    protected static function init()
    {
        self::$directory = \Stormpath\Resource\Directory::instantiate(array('name' => makeUniqueName('PasswordStrengthTest'), 'description' => 'Main Directory description'));
        self::createResource(\Stormpath\Resource\Directory::PATH, self::$directory);
        self::$passwordStrength = self::$directory->passwordPolicy->strength;
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
    public function can_get_password_strength_based_from_href()
    {
        $href = self::$directory->passwordPolicy->strength->href;

        $passwordStrength = \Stormpath\Directory\PasswordStrength::get($href);

        $this->assertInstanceOf(\Stormpath\Directory\PasswordStrength::class, $passwordStrength);
    }

    /** @test */
    public function href_is_accessible()
    {
        $this->assertContains('/strength', self::$passwordStrength->getHref());
        $this->assertContains('/strength', self::$passwordStrength->href);
    }
    
    /** @test */
    public function min_length_is_accessible()
    {
        $this->assertEquals(8, self::$passwordStrength->getMinLength());
        $this->assertEquals(8, self::$passwordStrength->minLength);
    }

    /** @test */
    public function min_length_is_savable()
    {
        self::$passwordStrength->setMinLength(6)->save();
        $passwordStrength = \Stormpath\Directory\PasswordStrength::get(self::$passwordStrength->href);
        $this->assertEquals(6, $passwordStrength->getMinLength());


        self::$passwordStrength->minLength = 8;
        self::$passwordStrength->save();
        $passwordStrength = \Stormpath\Directory\PasswordStrength::get(self::$passwordStrength->href);
        $this->assertEquals(8, $passwordStrength->getMinLength());
    }


    /** @test */
    public function max_length_is_accessible()
    {
        $this->assertEquals(100, self::$passwordStrength->getMaxLength());
        $this->assertEquals(100, self::$passwordStrength->maxLength);
    }

    /** @test */
    public function max_length_is_savable()
    {
        self::$passwordStrength->setMaxLength(50)->save();
        $passwordStrength = \Stormpath\Directory\PasswordStrength::get(self::$passwordStrength->href);
        $this->assertEquals(50, $passwordStrength->getMaxLength());


        self::$passwordStrength->maxLength = 100;
        self::$passwordStrength->save();
        $passwordStrength = \Stormpath\Directory\PasswordStrength::get(self::$passwordStrength->href);
        $this->assertEquals(100, $passwordStrength->getMaxLength());
    }

    /** @test */
    public function min_lower_case_is_accessible()
    {
        $this->assertEquals(1, self::$passwordStrength->getMinLowerCase());
        $this->assertEquals(1, self::$passwordStrength->minLowerCase);
    }

    /** @test */
    public function min_lower_case_is_savable()
    {
        self::$passwordStrength->setMinLowerCase(0)->save();
        $passwordStrength = \Stormpath\Directory\PasswordStrength::get(self::$passwordStrength->href);
        $this->assertEquals(0, $passwordStrength->getMinLowerCase());


        self::$passwordStrength->minLowerCase = 1;
        self::$passwordStrength->save();
        $passwordStrength = \Stormpath\Directory\PasswordStrength::get(self::$passwordStrength->href);
        $this->assertEquals(1, $passwordStrength->getMinLowerCase());
    }

    /** @test */
    public function min_upper_case_is_accessible()
    {
        $this->assertEquals(1, self::$passwordStrength->getMinUpperCase());
        $this->assertEquals(1, self::$passwordStrength->minUpperCase);
    }

    /** @test */
    public function min_upper_case_is_savable()
    {
        self::$passwordStrength->setMinUpperCase(0)->save();
        $passwordStrength = \Stormpath\Directory\PasswordStrength::get(self::$passwordStrength->href);
        $this->assertEquals(0, $passwordStrength->getMinUpperCase());


        self::$passwordStrength->minUpperCase = 1;
        self::$passwordStrength->save();
        $passwordStrength = \Stormpath\Directory\PasswordStrength::get(self::$passwordStrength->href);
        $this->assertEquals(1, $passwordStrength->getMinUpperCase());
    }

    /** @test */
    public function min_numeric_is_accessible()
    {
        $this->assertEquals(1, self::$passwordStrength->getMinNumeric());
        $this->assertEquals(1, self::$passwordStrength->minNumeric);
    }

    /** @test */
    public function min_numeric_is_savable()
    {
        self::$passwordStrength->setMinNumeric(0)->save();
        $passwordStrength = \Stormpath\Directory\PasswordStrength::get(self::$passwordStrength->href);
        $this->assertEquals(0, $passwordStrength->getMinNumeric());


        self::$passwordStrength->minNumeric = 1;
        self::$passwordStrength->save();
        $passwordStrength = \Stormpath\Directory\PasswordStrength::get(self::$passwordStrength->href);
        $this->assertEquals(1, $passwordStrength->getMinNumeric());
    }

    /** @test */
    public function min_symbol_is_accessible()
    {
        $this->assertEquals(0, self::$passwordStrength->getMinSymbol());
        $this->assertEquals(0, self::$passwordStrength->minSymbol);
    }

    /** @test */
    public function min_symbol_is_savable()
    {
        self::$passwordStrength->setMinSymbol(1)->save();
        $passwordStrength = \Stormpath\Directory\PasswordStrength::get(self::$passwordStrength->href);
        $this->assertEquals(1, $passwordStrength->getMinSymbol());


        self::$passwordStrength->minSymbol = 0;
        self::$passwordStrength->save();
        $passwordStrength = \Stormpath\Directory\PasswordStrength::get(self::$passwordStrength->href);
        $this->assertEquals(0, $passwordStrength->getMinSymbol());
    }

    /** @test */
    public function min_diacritic_is_accessible()
    {
        $this->assertEquals(0, self::$passwordStrength->getMinDiacritic());
        $this->assertEquals(0, self::$passwordStrength->minDiacritic);
    }

    /** @test */
    public function min_diacritic_is_savable()
    {
        self::$passwordStrength->setMinDiacritic(1)->save();
        $passwordStrength = \Stormpath\Directory\PasswordStrength::get(self::$passwordStrength->href);
        $this->assertEquals(1, $passwordStrength->getMinDiacritic());


        self::$passwordStrength->minDiacritic = 0;
        self::$passwordStrength->save();
        $passwordStrength = \Stormpath\Directory\PasswordStrength::get(self::$passwordStrength->href);
        $this->assertEquals(0, $passwordStrength->getMinDiacritic());
    }

    /** @test */
    public function prevent_reuse_is_accessible()
    {
        $this->assertEquals(0, self::$passwordStrength->getPreventReuse());
        $this->assertEquals(0, self::$passwordStrength->preventReuse);
    }

    /** @test */
    public function prevent_reuse_is_savable()
    {
        self::$passwordStrength->setPreventReuse(1)->save();
        $passwordStrength = \Stormpath\Directory\PasswordStrength::get(self::$passwordStrength->href);
        $this->assertEquals(1, $passwordStrength->getPreventReuse());


        self::$passwordStrength->preventReuse = 0;
        self::$passwordStrength->save();
        $passwordStrength = \Stormpath\Directory\PasswordStrength::get(self::$passwordStrength->href);
        $this->assertEquals(0, $passwordStrength->getPreventReuse());
    }



    

    
    

    

}