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


use Stormpath\Cache\NullCacheManager;
use Stormpath\Resource\Account;
use Stormpath\Resource\Application;
use Stormpath\Resource\Directory;
use Stormpath\Resource\FacebookProvider;
use Stormpath\Resource\GoogleProvider;
use Stormpath\Resource\GithubProvider;
use Stormpath\Resource\LinkedInProvider;
use Stormpath\Resource\Provider;
use Stormpath\Stormpath;

class ProviderTest extends \Stormpath\Tests\TestCase
{
    public function testGetGoogleProvider()
    {
        $clientId = makeUniqueName("mockClientId");
        $clientSecret = makeUniqueName("mockClientSecret");
        $redirectUri = "https://www.example.com/oauth2callback";

        $provider = self::$client->dataStore->instantiate(\Stormpath\Stormpath::GOOGLE_PROVIDER);
        $provider->clientId = $clientId;
        $provider->clientSecret = $clientSecret;
        $provider->redirectUri = $redirectUri;

        $directoryName = makeUniqueName("my-google-directory-2");
        $directoryDescription = makeUniqueName("A Google directory");

        $directory = self::$client->dataStore->instantiate(\Stormpath\Stormpath::DIRECTORY);
        $directory->name = $directoryName;
        $directory->description = $directoryDescription;
        $directory->provider = $provider;

        $tenant = self::$client->getCurrentTenant();
        $returnedDirectory = $tenant->createDirectory($directory);

        $this->assertNotNull($returnedDirectory);

        $returnedProvider = self::$client->dataStore->getResource(
            $returnedDirectory->href."/".Provider::PATH,
            \Stormpath\Stormpath::GOOGLE_PROVIDER
        );

        $this->assertEquals(GoogleProvider::GOOGLE_PROVIDER_ID,
            $returnedProvider->providerId);
        $this->assertNotEmpty($returnedProvider->createdAt);
        $this->assertNotEmpty($returnedProvider->modifiedAt);
        $this->assertEquals($clientId, $returnedProvider->clientId);
        $this->assertEquals($clientSecret, $returnedProvider->clientSecret);
        $this->assertEquals($redirectUri, $returnedProvider->redirectUri);

        $tokens = explode('/', $returnedDirectory->href);
        $directoryId = end($tokens);
        $this->assertEquals($returnedProvider, GoogleProvider::get(Directory::PATH.'/'.$directoryId.'/'.Provider::PATH));
        $this->assertEquals($returnedProvider, GoogleProvider::get($returnedDirectory->href.'/'.Provider::PATH));

        $this->assertEquals($returnedProvider, GoogleProvider::get($returnedDirectory->href));

        $this->assertEquals($returnedProvider, GoogleProvider::get($directoryId));
        $this->assertEquals($returnedProvider, GoogleProvider::get($directoryId.'/'.Provider::PATH));

        $returnedDirectory->delete();
    }

    public function testGetFacebookProvider()
    {
        $clientId = makeUniqueName("mockClientId");
        $clientSecret = makeUniqueName("mockClientSecret");

        $provider = self::$client->dataStore->instantiate(\Stormpath\Stormpath::FACEBOOK_PROVIDER);
        $provider->clientId = $clientId;
        $provider->clientSecret = $clientSecret;

        $directoryName = makeUniqueName("my-facebook-directory-2");
        $directoryDescription = makeUniqueName("A Facebook directory");

        $directory = self::$client->dataStore->instantiate(\Stormpath\Stormpath::DIRECTORY);
        $directory->name = $directoryName;
        $directory->description = $directoryDescription;
        $directory->provider = $provider;

        $tenant = self::$client->getCurrentTenant();
        $returnedDirectory = $tenant->createDirectory($directory);

        $this->assertNotNull($returnedDirectory);

        $returnedProvider = self::$client->dataStore->getResource(
            $returnedDirectory->href."/".Provider::PATH,
            \Stormpath\Stormpath::FACEBOOK_PROVIDER
        );

        $this->assertEquals(FacebookProvider::FACEBOOK_PROVIDER_ID,
            $returnedProvider->providerId);
        $this->assertNotEmpty($returnedProvider->createdAt);
        $this->assertNotEmpty($returnedProvider->modifiedAt);
        $this->assertEquals($clientId, $returnedProvider->clientId);
        $this->assertEquals($clientSecret, $returnedProvider->clientSecret);


        $tokens = explode('/', $returnedDirectory->href);
        $directoryId = end($tokens);
        $this->assertEquals($returnedProvider, FacebookProvider::get($directoryId));
        $this->assertEquals($returnedProvider, FacebookProvider::get($directoryId.'/'.Provider::PATH));
        $this->assertEquals($returnedProvider, FacebookProvider::get(Directory::PATH.'/'.$directoryId.'/'.Provider::PATH));
        $this->assertEquals($returnedProvider, FacebookProvider::get($returnedDirectory->href));
        $this->assertEquals($returnedProvider, FacebookProvider::get($returnedDirectory->href.'/'.Provider::PATH));

        $returnedDirectory->delete();
    }

    public function testGetGithubProvider()
    {
        $clientId = "mockClientId".md5(time().microtime().uniqid());
        $clientSecret = "mockClientSecret".md5(time().microtime().uniqid());

        $provider = self::$client->dataStore->instantiate(\Stormpath\Stormpath::GITHUB_PROVIDER);
        $provider->clientId = $clientId;
        $provider->clientSecret = $clientSecret;

        $directoryName = "my-github-directory-2".md5(time().microtime().uniqid());
        $directoryDescription = "A Github directory".md5(time().microtime().uniqid());

        $directory = self::$client->dataStore->instantiate(\Stormpath\Stormpath::DIRECTORY);
        $directory->name = $directoryName;
        $directory->description = $directoryDescription;
        $directory->provider = $provider;

        $tenant = self::$client->getCurrentTenant();
        $returnedDirectory = $tenant->createDirectory($directory);

        $this->assertNotNull($returnedDirectory);

        $returnedProvider = self::$client->dataStore->getResource(
            $returnedDirectory->href."/".Provider::PATH,
            \Stormpath\Stormpath::GITHUB_PROVIDER
        );

        $this->assertEquals(GithubProvider::GITHUB_PROVIDER_ID,
            $returnedProvider->providerId);
        $this->assertNotEmpty($returnedProvider->createdAt);
        $this->assertNotEmpty($returnedProvider->modifiedAt);
        $this->assertEquals($clientId, $returnedProvider->clientId);
        $this->assertEquals($clientSecret, $returnedProvider->clientSecret);


        $tokens = explode('/', $returnedDirectory->href);
        $directoryId = end($tokens);
        $this->assertEquals($returnedProvider, GithubProvider::get($directoryId));
        $this->assertEquals($returnedProvider, GithubProvider::get($directoryId.'/'.Provider::PATH));
        $this->assertEquals($returnedProvider, GithubProvider::get(Directory::PATH.'/'.$directoryId.'/'.Provider::PATH));
        $this->assertEquals($returnedProvider, GithubProvider::get($returnedDirectory->href));
        $this->assertEquals($returnedProvider, GithubProvider::get($returnedDirectory->href.'/'.Provider::PATH));

        $returnedDirectory->delete();
    }


    public function testGetLinkedInProvider()
    {
        $clientId = "mockClientId".md5(time().microtime().uniqid());
        $clientSecret = "mockClientSecret".md5(time().microtime().uniqid());

        $provider = self::$client->dataStore->instantiate(\Stormpath\Stormpath::GITHUB_PROVIDER);
        $provider->clientId = $clientId;
        $provider->clientSecret = $clientSecret;

        $directoryName = "my-linkedin-directory-2".md5(time().microtime().uniqid());
        $directoryDescription = "A LinkedIn directory".md5(time().microtime().uniqid());

        $directory = self::$client->dataStore->instantiate(\Stormpath\Stormpath::DIRECTORY);
        $directory->name = $directoryName;
        $directory->description = $directoryDescription;
        $directory->provider = $provider;

        $tenant = self::$client->getCurrentTenant();
        $returnedDirectory = $tenant->createDirectory($directory);

        $this->assertNotNull($returnedDirectory);

        $returnedProvider = self::$client->dataStore->getResource(
            $returnedDirectory->href."/".Provider::PATH,
            \Stormpath\Stormpath::LINKEDIN_PROVIDER
        );

        $this->assertEquals(LinkedInProvider::LINKEDIN_PROVIDER_ID,
            $returnedProvider->providerId);
        $this->assertNotEmpty($returnedProvider->createdAt);
        $this->assertNotEmpty($returnedProvider->modifiedAt);
        $this->assertEquals($clientId, $returnedProvider->clientId);
        $this->assertEquals($clientSecret, $returnedProvider->clientSecret);


        $tokens = explode('/', $returnedDirectory->href);
        $directoryId = end($tokens);
        $this->assertEquals($returnedProvider, LinkedInProvider::get($directoryId));
        $this->assertEquals($returnedProvider, LinkedInProvider::get($directoryId.'/'.Provider::PATH));
        $this->assertEquals($returnedProvider, LinkedInProvider::get(Directory::PATH.'/'.$directoryId.'/'.Provider::PATH));
        $this->assertEquals($returnedProvider, LinkedInProvider::get($returnedDirectory->href));
        $this->assertEquals($returnedProvider, LinkedInProvider::get($returnedDirectory->href.'/'.Provider::PATH));

        $returnedDirectory->delete();
    }


    public function testGoogleProviderAccount()
    {
        $requestExecutor = $this->getMock('\Stormpath\Http\RequestExecutor');
        $apiKey = $this->getMock('\Stormpath\ApiKey', array(), array("mockId", "mockSecret"));
        $cacheManager = $this->getMock('\Stormpath\Cache\CacheManager');
        $dataStore = $this->getMock('\Stormpath\DataStore\DefaultDataStore',
            array('create'), array($requestExecutor, $apiKey, $cacheManager));

        $code = "4/XrsKzIJuy3ye57eqbanlQDN1wZHYfaUV-MFyC6dRjRw.wnCoOEKwnlwXXmXvfARQvthKMCbPmgI";
        $providerAccountRequest = new \Stormpath\Provider\GoogleProviderAccountRequest(array(
            "code" => $code
        ));

        $providerData = $providerAccountRequest->getProviderData($dataStore);

        $this->assertEquals(GoogleProvider::GOOGLE_PROVIDER_ID, $providerData->providerId);
        $this->assertEquals($code, $providerData->code);
        $this->assertEmpty($providerData->accessToken);

        $providerAccountAccess = $dataStore->instantiate(Stormpath::PROVIDER_ACCOUNT_ACCESS);
        $providerAccountAccess->providerData = $providerData;

        $application = new Application($dataStore);

        $providerAccountResult = $this->getMock('\Stormpath\Resource\ProviderAccountResult');
        $dataStore->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo($application->getHref().'/'.Account::PATH),
                $this->equalTo($providerAccountAccess),
                $this->equalTo(Stormpath::PROVIDER_ACCOUNT_RESULT)
            )
            ->will($this->returnValue($providerAccountResult));


        $returnedResult = $application->getAccount($providerAccountRequest);

        $this->assertEquals($providerAccountResult, $returnedResult);
    }

    public function testFacebookProviderAccount()
    {
        $requestExecutor = $this->getMock('\Stormpath\Http\RequestExecutor');
        $apiKey = $this->getMock('\Stormpath\ApiKey', array(), array("mockId", "mockSecret"));
        $cacheManager = $this->getMock('\Stormpath\Cache\CacheManager');
        $dataStore = $this->getMock('\Stormpath\DataStore\DefaultDataStore',
            array('create'), array($requestExecutor, $apiKey, $cacheManager));

        $accessToken = "4/XrsKzIJuy3ye57eqbanlQDN1wZHYfaUV-MFyC6dRjRw.wnCoOEKwnlwXXmXvfARQvthKMCbPmgI";
        $providerAccountRequest = new \Stormpath\Provider\FacebookProviderAccountRequest(array(
            "accessToken" => $accessToken
        ));

        $providerData = $providerAccountRequest->getProviderData($dataStore);

        $this->assertEquals(FacebookProvider::FACEBOOK_PROVIDER_ID, $providerData->providerId);
        $this->assertEquals($accessToken, $providerData->accessToken);

        $providerAccountAccess = $dataStore->instantiate(Stormpath::PROVIDER_ACCOUNT_ACCESS);
        $providerAccountAccess->providerData = $providerData;

        $application = new Application($dataStore);

        $providerAccountResult = $this->getMock('\Stormpath\Resource\ProviderAccountResult');
        $dataStore->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo($application->getHref().'/'.Account::PATH),
                $this->equalTo($providerAccountAccess),
                $this->equalTo(Stormpath::PROVIDER_ACCOUNT_RESULT)
            )
            ->will($this->returnValue($providerAccountResult));


        $returnedResult = $application->getAccount($providerAccountRequest);

        $this->assertEquals($providerAccountResult, $returnedResult);
    }

    public function testGithubProviderAccount()
    {
        $requestExecutor = $this->getMock('\Stormpath\Http\RequestExecutor');
        $apiKey = $this->getMock('\Stormpath\ApiKey', array(), array("mockId", "mockSecret"));
        $cacheManager = $this->getMock('\Stormpath\Cache\CacheManager');
        $dataStore = $this->getMock('\Stormpath\DataStore\DefaultDataStore',
            array('create'), array($requestExecutor, $apiKey, $cacheManager));

        $accessToken = "4/XrsKzIJuy3ye57eqbanlQDN1wZHYfaUV-MFyC6dRjRw.wnCoOEKwnlwXXmXvfARQvthKMCbPmgI";
        $providerAccountRequest = new \Stormpath\Provider\GithubProviderAccountRequest(array(
            "accessToken" => $accessToken
        ));

        $providerData = $providerAccountRequest->getProviderData($dataStore);

        $this->assertEquals(GithubProvider::GITHUB_PROVIDER_ID, $providerData->providerId);
        $this->assertEquals($accessToken, $providerData->accessToken);

        $providerAccountAccess = $dataStore->instantiate(Stormpath::PROVIDER_ACCOUNT_ACCESS);
        $providerAccountAccess->providerData = $providerData;

        $application = new Application($dataStore);

        $providerAccountResult = $this->getMock('\Stormpath\Resource\ProviderAccountResult');
        $dataStore->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo($application->getHref().'/'.Account::PATH),
                $this->equalTo($providerAccountAccess),
                $this->equalTo(Stormpath::PROVIDER_ACCOUNT_RESULT)
            )
            ->will($this->returnValue($providerAccountResult));


        $returnedResult = $application->getAccount($providerAccountRequest);

        $this->assertEquals($providerAccountResult, $returnedResult);
    }

    public function testLinkedInProviderAccount()
    {
        $requestExecutor = $this->getMock('\Stormpath\Http\RequestExecutor');
        $apiKey = $this->getMock('\Stormpath\ApiKey', array(), array("mockId", "mockSecret"));
        $cacheManager = $this->getMock('\Stormpath\Cache\CacheManager');
        $dataStore = $this->getMock('\Stormpath\DataStore\DefaultDataStore',
            array('create'), array($requestExecutor, $apiKey, $cacheManager));

        $accessToken = "4/XrsKzIJuy3ye57eqbanlQDN1wZHYfaUV-MFyC6dRjRw.wnCoOEKwnlwXXmXvfARQvthKMCbPmgI";
        $providerAccountRequest = new \Stormpath\Provider\LinkedInProviderAccountRequest(array(
            "accessToken" => $accessToken
        ));

        $providerData = $providerAccountRequest->getProviderData($dataStore);

        $this->assertEquals(LinkedInProvider::LINKEDIN_PROVIDER_ID, $providerData->providerId);
        $this->assertEquals($accessToken, $providerData->accessToken);

        $providerAccountAccess = $dataStore->instantiate(Stormpath::PROVIDER_ACCOUNT_ACCESS);
        $providerAccountAccess->providerData = $providerData;

        $application = new Application($dataStore);

        $providerAccountResult = $this->getMock('\Stormpath\Resource\ProviderAccountResult');
        $dataStore->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo($application->getHref().'/'.Account::PATH),
                $this->equalTo($providerAccountAccess),
                $this->equalTo(Stormpath::PROVIDER_ACCOUNT_RESULT)
            )
            ->will($this->returnValue($providerAccountResult));


        $returnedResult = $application->getAccount($providerAccountRequest);

        $this->assertEquals($providerAccountResult, $returnedResult);
    }
}