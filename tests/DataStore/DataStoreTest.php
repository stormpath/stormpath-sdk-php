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

namespace Stormpath\Tests\DataStore;

class DataStoreTest extends \Stormpath\Tests\TestCase {
	private static $application;
	private static $inited;

	private static $account;

	protected static function init()
	{
		self::$application = \Stormpath\Resource\Application::instantiate(array('name' => makeUniqueName('ApplicationTest'), 'description' => 'Description of Main App', 'status' => 'enabled'));
		self::createResource(\Stormpath\Resource\Application::PATH, self::$application, array('createDirectory' => true));
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
		if (self::$application && self::$application->href)
		{
			self::$application->delete();
		}

		parent::tearDownAfterClass();
	}


	/**
	 * @test
	 * @expectedException \InvalidArgumentException
	 */
	public function can_not_make_request_to_a_url_other_than_clients_base_url()
	{
	     $application = \Stormpath\Resource\Application::get('http://test.tld/applications/123');
	}

	/** @test */
	public function can_make_request_to_url_that_is_base_url()
	{
		$application = \Stormpath\Resource\Application::get(self::$application->href);
		$this->assertInstanceOf(\Stormpath\Resource\Application::class, $application);
	}

	
	

}