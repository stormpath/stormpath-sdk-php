[![Build Status](https://api.travis-ci.org/stormpath/stormpath-sdk-php.png?branch=master,dev)](https://travis-ci.org/stormpath/stormpath-sdk-php)

# Stormpath PHP SDK
Stormpath is the first easy, secure user management and authentication service for developers. This is the PHP SDK to ease integration of its features with any PHP language based application.

## Installation

You can install **stormpath-sdk-php** via Composer or by downloading the source.

### Via Composer:

**stormpath-sdk-php** is available on Packagist as the [stormpath/sdk][stormpath-packagist] package.

On your project root, install Composer

    curl -sS https://getcomposer.org/installer | php

Configure the **stormpath/sdk** dependency in your 'composer.json' file:

    "require": {
        "stormpath/sdk": "1.0.*@beta"
    }

On your project root, install the the SDK with its dependencies:

    php composer.phar install
    
### Download the source code

Download the [master branch zip file][sdk-zip] which includes the latest version
of the SDK and its dependencies, with the exception of Guzzle (the Http client).

The Guzzle Http dependency can be installed via PEAR (version 3.7.4 is the minimum required).
[Click here to visit the Guzzle PEAR installation instructions.][guzzle-installation-pear]

Once you download the library, move the stormpath-sdk-php folder to your project directory and then include the library file:

    require '/path/to/stormpath-sdk-php/src/Stormpath/Stormpath.php';

You're ready to connect with Stormpath!

## Provision Your Stormpath Account

If you have not already done so, register as a developer on
[Stormpath][stormpath] and set up your API credentials and resources:

1. Create a [Stormpath][stormpath] developer account and
   [create your API Keys][create-api-keys] downloading the
   <code>apiKey.properties</code> file into a <code>.stormpath</code>
   folder under your local home directory.

2. Through the [Stormpath Admin UI][stormpath-admin-login], create yourself
   an [Application Resource][concepts]. On the Create New Application
   screen, make sure the "Create a new directory with this application" box
   is checked. This will provision a [Directory Resource][concepts] along
   with your new Application Resource and link the Directory to the
   Application as an [Account Store][concepts]. This will allow users
   associated with that Directory Resource to authenticate and have access
   to that Application Resource.

   It is important to note that although your developer account comes with
   a built-in Application Resource (called "Stormpath") - you will still
   need to provision a separate Application Resource.

3. Take note of the _REST URL_ of the Application you just created. Your
   web application will communicate with the Stormpath API in the context
   of this one Application Resource (operations such as: user-creation,
   authentication, etc.).

## Getting Started

1.  **Require the Stormpath PHP SDK** via the composer auto loader

    ```php
    require 'vendor/autoload.php';
    ```

2.  **Configure the client** using the API key properties file

    ```php
    \Stormpath\Client::$apiKeyFileLocation = $_SERVER['HOME'] . '/.stormpath/apiKey.properties';
    ```

3.  **List all your applications and directories**

    ```php
    $tenant = \Stormpath\Resource\Tenant::get();
    foreach($tenant->applications as $app)
    {
        print $app->name;
    }

    foreach($tenant->directories as $dir)
    {
        print $dir->name;
    }
    ```

4.  **Get access to the specific application and directory** using a specific href.

    ```php
    $application = \Stormpath\Resource\Application::get($applicationHref);

    $directory = \Stormpath\Resource\Directory::get($directoryHref);
    ```

5.  **Create an application** and auto create a directory as the account store.

    ```php
    $application = \Stormpath\Resource\Application::create(
      array('name' => 'May Application',
            'description' => 'My Application Description'),
      array('createDirectory' => true)
       );
    ```

6.  **Create an account for a user** on the directory.

    ```php
    $account = \Stormpath\Resource\Account::instantiate(
      array('givenName' => 'John',
            'surname' => 'Smith',
            'username' => 'johnsmith',
            'email' => 'john.smith@example.com',
            'password' => '4P@$$w0rd!'));

    $application->createAccount($account);
    ```

7.  **Update an account**

    ```php
    $account->givenName = 'Johnathan';
    $account->middleName = 'A.';
    $account->save();
    ```

8.  **Authenticate the Account** for use with an application:

    ```php
    try {

        $result = $application->authenticate('johnsmith', '4P@$$w0rd!');
        $account = $result->account;

    } catch (\Stormpath\Resource\ResourceError $re)
    {
        print $re->getStatus();
        print $re->getErrorCode();
        print $re->getMessage();
        print $re->getDeveloperMessage();
        print $re->getMoreInfo();
    }
    ```

9.  **Send a password reset request**

    ```php
    $application->sendPasswordResetEmail('john.smith@example.com');
    ```

10.  **Create a group** in a directory

    ```php
    $group = \Stormpath\Resource\Group::instantiate(array('name' => 'Admins'));

    $directory->createGroup($group);
    ```

11.  **Add the account to the group**

    ```php
    $group->addAccount($account);
    ```

12. **Check for account inclusion in group**

    ```php
    $isAdmin = false;
    $search = array('name' => 'Admins');

    foreach($account->groups->setSearch($search) as $group)
    {
        // if one group was returned, the account is in
        // the group based on the search criteria
        $isAdmin = true;
    }
    ```

## Common Uses

### Creating a client

All Stormpath features are accessed through a
<code>stormpath.Client</code> instance, or a resource
created from one. A client needs an API key (made up of an _id_ and a
_secret_) from your Stormpath developer account to manage resources
on that account. That API key can be specified in many ways
when configuring the Client:

* The location of API key properties file:

  ```php
  // This can also be an http location
  $apiKeyFileLocation = '/some/path/to/apiKey.properties';
  \Stormpath\Client::$apiKeyFileLocation = $apiKeyFileLocation;

  //Or

  $builder = new \Stormpath\ClientBuilder();
  $client = $builder->setApiKeyFileLocation($apiKeyFileLocation)->build();
  ```

  You can even identify the names of the properties to use as the API
  key id and secret. For example, suppose your properties were:

  ```php
  $foo = 'APIKEYID';
  $bar = 'APIKEYSECRET';
  ```

  You could configure the Client with the following parameters:

  ```php
  \Stormpath\Client::$apiKeyIdPropertyName = $foo;
  \Stormpath\Client::$apiKeySecretPropertyName = $bar;

  //Or

  $builder = new \Stormpath\ClientBuilder();
  $client = $builder->setApiKeyFileLocation($apiKeyFileLocation)->
                      setApiKeyIdPropertyName($foo)->
                      setApiKeySecretPropertyName($bar)->
                      build();
  ```

* Passing in a valid PHP ini formatted string:

  ```php
  $apiKeyProperties = "apiKey.id=APIKEYID\napiKey.secret=APIKEYSECRET";
  \Stormpath\Client::$apiKeyProperties = $apiKeyProperties;

  //Or

  $builder = new \Stormpath\ClientBuilder();
  $client = $builder->setApiKeyProperties($apiKeyProperties)->build();
  ```

  Working with different properties names (explained in the previous config instructions)
  also work with this scenario.

* Passing in an ApiKey instance:

  ```php
  $apiKey = new \Stormpath\ApiKey('APIKEYID', 'APIKEYSECRET');
  $client = new \Stormpath\Client($apiKey);
  ```

**Note**: Only if the client is configured using the static properties,
the static calls to resources will run successfully. If the Client is directly
instantiated (using the ClientBuilder or the Client constructor), the Client
instance must be used to start interactions with Stormpath.

### Cache
By default all items will be cached.  This will use Array caching if nothing is defined.
The caching happens automatically for you so you do not have to think about it.  NOTE: Array caching will 
only store an item in cache within an array.  This means an item will only be available within the cache
for a single request in the application.  Array Caching does not persist across multiple requests.
There are many reason why you may want to cache api calls to the Stormpath API where the main reason is speed.
Caching allows you to make one api call over a set amount of time while all subsequent calls to
the same endpoint will pull data that was cached.  By default, the time to live is set to 1 hour, 
while the time to idle is set to 2 hours.

All of you default options can be overridden by passing an options array during the ClientBuilder.
The following is the default array that is provided to the ClientBuilder if no options are overridden.

  ```php
  $cacheManagerOptions = array(
      'cachemanager' => 'Array', //Array, Memcached, Redis, Null, or the full namespaced CacheManager instance
      'memcached' => array(
          array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
      ),
      'redis' => array(
          'host' => '127.0.0.1',
          'port' => 6379,
          'password' => NULL
      ),
      'ttl' => 60, // This value is set in minutes
      'tti' => 120, // This value is set in minutes
      'regions' => array(
        'accounts' => array(
            'ttl' => 60,
            'tti' => 120
         ),
        'applications' => array(
            'ttl' => 60,
            'tti' => 120
         ),
        'directories' => array(
            'ttl' => 60,
            'tti' => 120
         ),
        'groups' => array(
            'ttl' => 60,
            'tti' => 120
         ),
        'tenants' => array(
            'ttl' => 60,
            'tti' => 120
         ),
        
      ) 
  );
  ```
Only the values you wish to override would need to be supplied in the array that 
is passed to the ClientBuilder.


The following coule be used to on the Client to set options for the default
Memory cacheManager:

 ```php
  \Stormpath\Client::$cacheManagerOptions = $cacheManagerOptions;

  //Or

  $builder = new \Stormpath\ClientBuilder();
  $client = $builder->setCacheManagerOptions($cacheManagerOptions)->
                      build();
  ```
  
It is just as easy to set a cache manager to override the default Memory.

You could configure the client with the following:

  ```php
  \Stormpath\Client::$cacheManager = 'Memcached';
  \Stormpath\Client::$cacheManagerOptions = $cacheManagerOptions;
  ```
Doing it this way, the option in the array for the CacheManager will not be used.  Setting
the CacheManger statically will override the option set in the CacheManagerOptions.

You can also call it without static calls as follows:
   ```php
   $builder = new \Stormpath\ClientBuilder();
   $client = $builder->setCacheManager('Memcached')-> //setting this will ignore the 'cachemanager' in options array
                       setCacheManagerOptions($cacheManagerOptions)->
                     build();
   ```
   
 In the previous examples, setting the CacheManager either statically or in the 'setCacheManager' method,
 the key 'cachemanager' in the $cacheManagerOptions array will be ignored.
 
 
### Disable Cache
Although this is not suggested as it will make more calls and slow your application, you can disable the Cache.
 This can be accomplished by doing any of the above for setting the cache manager, however you will set it to 
 be the null cache manager.  This can be done by setting the manager to `Null`
 
### Extending Cache
Extending the Cache Manager to supply your own caching is very easy.  There are only a couple
files that are required.  A file that implements `Stormpath\Cache\Cache` and a file that
implements `Stormpath\Cache\CacheManager`.  Take the following if we wanted to create an 
Array caching system.

  ```php
    class ArrayCacheManager implements CacheManager {
         public function getCache() { ... }
    }
  
    class ArrayCache implements Cache {
        public function get($key) { ... }
        public function put($key, $value, $minutes) { ... }
        public function delete($key) { ... }
        public function clear() { ... }
    }
  ```
    
Then you would call it the same way you do for a cache manager normally.  

### Accessing Resources

Most of the work you do with Stormpath is done through the applications
and directories you have registered. You use the static getter of resource classes
to access them with their REST URL, or via the data store of the client instance:

  ```php
  $application = \Stormpath\Resource\Application::get($applicationHref);

  $directory = \Stormpath\Resource\Directory::get($directoryHref);

  //Or

  $application = $client->
                 dataStore->
                 getResource($applicationHref, \Stormpath\Stormpath::APPLICATION);

  $directory = $client->
               dataStore->
               getResource($directoryHref, \Stormpath\Stormpath::DIRECTORY);
  ```

Additional resources are <code>Account</code>, <code>Group</code>,
<code>GroupMemberships</code>, <code>AccountStoreMappings</code>, and the single reference to your
<code>Tenant</code>.

### Creating Resources

Applications, directories and account store mappings can be created directly off their resource classes;
or from the tenant resource (or from the application, in the case of account store mapping).

  ```php
  $application = \Stormpath\Resource\Application::create(
    array('name' => 'My App',
          'description' => 'App Description')
    );

  $directory = \Stormpath\Resource\Directory::create(
    array('name' => 'My directory',
          'description' => 'My directory description')
    );

  $accountStoreMapping = \Stormpath\Resource\AccountStoreMapping::create(
    array('accountStore' => $directory, // this could also be a group
          'application' => $application)
    );

  //Or

  $application = $client->
                 dataStore->
                 instantiate(\Stormpath\Stormpath::APPLICATION);
  $application->name = 'My App';
  $application->description = 'App Description';
  $tenant->createApplication($application);

  $directory = $client->
               dataStore->
               instantiate(\Stormpath\Stormpath::DIRECTORY);
  $directory->name = 'My directory';
  $directory->description = 'Directory Description';
  $tenant->createDirectory($directory);

  $accountStoreMapping = $client->
                         dataStore->
                         instantiate(\Stormpath\Stormpath::ACCOUNT_STORE_MAPPING);
  $accountStoreMapping->accountStore = $directory; // this could also be a group
  $application->createAccountStoreMapping($accountStoreMapping);
  ```
  
### Custom Data
You may find it useful to store your own custom data on resource's.  With the Stormpath SDK, we make it easy to do so. 
All resource's support a Custom Data collection for your own custom use.  The CustomData resource is a schema-less JSON 
object (aka ‘map’, ‘associative array’, or ‘dictionary’) that allows you to specify whatever name/value pairs you wish.

The CustomData resource is always connected to a resource and you can always reach it by calling customData magic method 
(alias to getCustomData()) on the resource instance:

```php
  $application = \Stormpath\Resource\Application::get($applicationHref);

  $applicationCustomData = $application->customData;

  //Or

  $application = $client->
                 dataStore->
                 getResource($applicationHref, \Stormpath\Stormpath::APPLICATION);
                 
  $applicationCustomData = $application->customData;

  ```
  
In addition to your custom name/value pairs, a CustomData resource will always contain 3 reserved read-only fields:
* href: The fully qualified location of the custom data resource
* createdAt: the UTC timestamp with millisecond precision of when the resource was created in Stormpath as an ISO 8601 formatted string, for example 2017-04-01T14:35:16.235Z
* modifiedAt: the UTC timestamp with millisecond precision of when the resource was last updated in Stormpath as an ISO 8601 formatted string.

You can store an unlimited number of additional name/value pairs in the CustomData resource, with the following restrictions:

* The total storage size of a single CustomData resource cannot exceed 10 MB (megabytes). The href, createdAt and modifiedAt field names and values do not count against your resource size quota.
* Field names must:
    * be 1 or more characters long, but less than or equal to 255 characters long (1 <= N <= 255).
    * contain only alphanumeric characters 0-9A-Za-z, underscores _ or dashes - but cannot start with a dash -.
    * may not equal any of the following reserved names: href, createdAt, modifiedAt, meta, spMeta, spmeta, ionmeta, or ionMeta.
    
#### Create Custom Data
An example of creating an account with custom data
```php 
  $account = $client->dataStore->instantiate(\Stormpath\Stormpath::ACCOUNT);
  $account->email = 'john.smith@example.com';
  $account->givenName = 'John';
  $account->password ='4P@$$w0rd!';
  $account->surname = 'Smith';
  $account->username = 'johnsmith';
  
  $customData = $account->customData;
  $customData->rank = "Captain";
  $customData->birthDate = "2305-07-13";
  $customData->favoriteDrink = "favoriteDrink";
  
  $directory->createAccount($account);
  ```
  

#### Retrieve Custom Data
An example of retrieving custom data for an application.

```php
  $application = $client->
                     dataStore->
                     getResource($applicationHref, \Stormpath\Stormpath::APPLICATION);
                     
  $applicationCustomData = $application->customData;
  ```
  
After you have access to the whole custom data resource, you can then retrieve a specific property with the following

```php
    $property = $applicationCustomData->property;
    ```
          
#### Update Custom Data
//TODO: Update to magic method
```php
  $customData->favoriteColor = "red";
  $customData->hobby = "Kendo";
  $customData->save();
  ```
 
  
#### Delete Custom Data
You may delete all of a resource's custom data by invoking the delete() method to the resource's CustomData
```php
   ${RESOURCE}->customData->delete();
   ```

This will delete all of the respective resource's custom data fields, but it leaves the CustomData 
placeholder in the resource. You cannot delete the CustomData resource entirely – it will be 
automatically permanently deleted when the resource is deleted.

#### Delete Custom Data Field
You may also delete an individual custom data field entirely by calling the remove() method on the resource's 
CustomData while stating the custom data field as a parameter.

```php
  $customData->remove("favoriteColor");
  ```


### Collections
#### Search

Resource collections can be searched by a general query string, or by attribute or by specifying a Search object.

Passing a string to the search method will filter by any attribute on the collection:

  ```php
  $applications = $tenant->applications;
  $applications->search = 'foo';

  //Or, use the Search object

  $search = new \Stormpath\Resource\Search();
  $search->filter = 'foo';
  $applications = $tenant->applications;
  $applications->search = $search;
  ```

To search a specific attribute or attributes, pass an array:

  ```php
  $applications = $tenant->applications;
  $applications->search = array('name' => '*foo*',
                                'description' => 'bar*',
                                'status' => 'enabled');

  //Or, use the Search object

  $search = new \Stormpath\Resource\Search();
  $applications = $tenant->applications;
  $applications->search = $search->addMatchAnywhere('name', 'foo')->
                                   addStartsWith('description', 'bar')->
                                   addEquals('status', 'enabled');
  ```

Alternatively, you can use the collection getter options to specify the search:

  ```php
  $applications = $tenant->getApplications(array('q' => 'foo'));
  ```

Now you can loop through the collection resource and get the results according to the specified search:

  ```php
  foreach($applications as $app)
  {
    print $app->name;
  }
  ```

#### Pagination

Collections can be paginated using chain-able methods (or by just using the magic setters and looping the collection). <code>offset</code> is the zero-based starting index in the entire collection of the first item to return. Default is 0.
<code>limit</code> is the maximum number of collection items to return for a single request. Minimum value is 1. Maximum value is 100. Default is 25.

  ```php
  foreach($tenant->applications->setOffset(10)->setLimit(100) as $app)
  {
    print $app->name;
  }
  ```

Alternatively, you can use the collection getter options to specify the pagination:

  ```php
  $applications = $tenant->getApplications(array('offset' => 10, 'limit' => 100));
  foreach($applications as $app)
  {
    print $app->name;
  }
  ```

#### Order

Collections can be ordered. In the following example, a paginated collection is ordered.

  ```php
  $applications = $tenant->applications;
  $applications->order = new \Stormpath\Resource\Order(array('name'), 'desc');
  ```

Or specify the order by string:

  ```php
  $applications = $tenant->applications;
  $applications->order = 'name desc';
  ```

Alternatively, you can use the collection getter options to specify the order:

  ```php
  $applications = $tenant->getApplications(array('orderBy' => 'name desc'));
  ```

Now you can loop through the collection resource and get the results according to the specified order:

  ```php
  foreach($applications as $app)
  {
    print $app->name;
  }
  ```

#### Entity Expansion

A resource's children can be eagerly loaded by passing the expansion string in the options argument of a call to a <code>getter</code>,
or setting the expansion in a collection resource.

  ```php
  $account = \Stormpath\Resource\Account::get(
    $accountHref,
    array('expand' => 'groups,groupMemberships'));

  //Or

  $account = $client->
             dataStore->
             getResource($accountHref,
                        \Stormpath\Stormpath::ACCOUNT,
                        array('expand' => 'groups,groupMemberships'));
  ```

For a collection resource:

  ```php

  $accounts = $directory->accounts;
  $expansion = new \Stormpath\Resource\Expansion();
  $accounts->expansion = $expansion->addProperty('directory')->addProperty('tenant');

  //Or

  $accounts->expansion = 'directory,tenant';

  //Or

  $accounts->expansion = array('directory', 'tenant');

  //Or

  $accounts = $directory->getAccounts(array('expand' => 'directory,tenant'));
  ```

<code>limit</code> and <code>offset</code> can be specified for each child resource by calling <code>addProperty</code>.

  ```php
  $expansion = new \Stormpath\Resource\Expansion();
  $expansion->addProperty('groups', array('offset' => 5, 'limit' => 10));
  $account = \Stormpath\Resource\Account::get(
    $accountHref,
    $expansion->toExpansionArray());

  //Or

  $account = $client->
             dataStore->
             getResource($accountHref,
                         \Stormpath\Stormpath::ACCOUNT, $expansion->toExpansionArray());
  ```

### Registering Accounts

Accounts are created on a directory instance. They can be created in two ways:

* Directly from a <code>directory</code> resource:

  ```php
  $account = \Stormpath\Resource\Account::instantiate(
    array('givenName' => 'John',
          'surname' => 'Smith',
          'email' => 'johnsmith@example.com',
          'password' => '4P@$$w0rd!'));

  $directory->createAccount($account);
  ```

* Creating it from an <code>application</code> resource that has a directory as the default account store:

  ```php
   $application->createAccount($account);
  ```

   We can specify an additional flag to indicate if the account
   can skip any registration workflow configured on the directory.

   ```php
   // Will skip workflow, if any
   $directory->createAccount($account, array('registrationWorkflowEnabled' => false));

   //Or

   $application->createAccount($account, array('registrationWorkflowEnabled' => false));
   ```

If the directory has been configured with an email verification workflow
and a non-Stormpath URL, you have to pass the verification token sent to
the URL in a <code>sptoken</code> query parameter back to Stormpath to
complete the workflow. This is done through the
<code>verifyEmailToken</code> static method of the <code>Client</code>, and an instance
method with the same name on the <code>Tenant</code> resource.


 ```php
  // this call returns an account object
  $account = \Stormpath\Client::verifyEmailToken('the_token_from_query_string');

  //Or

  // this call returns an account object
  $account = $tenant->verifyEmailToken('the_token_from_query_string');
  ```

### Authentication

Authentication is accomplished by passing a username or an email and a
password to <code>authenticate</code> of an application we've
registered on Stormpath. This will either return a
<code>\Stormpath\Resource\AuthenticationResult</code> instance if
the credentials are valid, or raise a <code>\Stormpath\Resource\ResourceError</code>
otherwise. In the former case, you can get the <code>account</code>
associated with the credentials.

```php
try {

    $result = $application->authenticate('johnsmith', '4P@$$w0rd!');
    $account = $result->account;

} catch (\Stormpath\Resource\ResourceError $re)
{
    print $re->getStatus();
    print $re->getErrorCode();
    print $re->getMessage();
    print $re->getDeveloperMessage();
    print $re->getMoreInfo();
}
```

#### Log In (Authenticate) an Account with Specific AccountStore

When you submit an authentication request to Stormpath, instead of executing the default login logic that cycles through account stores to find an account match, you can specify the `AccountStore` where the login attempt will be issued to.

At the time you create the request, it is likely that you may know the account store where the account resides, therefore you can target it directly. This will speed up the authentication attempt (especially if you have a very large number of account stores).

##### Example Request

```php
$accountStore = $anAccountStoreMapping->getAccountStore();
$authenticationRequest = new UsernamePasswordRequest('usernameOrEmail', 'password', 
    array('accountStore' => $accountStore));
$result = $application->authenticateAccount($authenticationRequest);
```

### Password Reset

A password reset workflow, if configured on the directory the account is
registered on, can be kicked off with the
<code>sendPasswordResetEmail</code> method on an application:

```php
// this method returns the account
$account = $application->sendPasswordResetEmail('super_unique_email@unknown123.kot');
```

If the workflow has been configured to verify through a non-Stormpath
URL, you can verify the token sent in the query parameter
<code>sptoken</code> with the <code>verifyPasswordResetToken</code>
method on the application.

```php
// this method returns the account
$account = $application->verifyPasswordResetToken('the_token_from_query_string');
end
```

With the account acquired you can then update the password:

```php
  $account->password = 'new_password';
  $account->save();
```

_NOTE :_ Confirming a new password is left up to the web application
code calling the Stormpath SDK. The SDK does not require confirmation.

### ACL through Groups

Memberships of accounts in certain groups can be used as an
authorization mechanism. As the <code>groups</code> collection property
on an account instance can be searched, you can seek for the group
to determine the account is associated with it:

```php
$isAdmin = false;
$search = array('name' => 'Admins');

foreach($account->groups->setSearch($search) as $group)
{
    // if one group was returned, the account is
    // in the group based on the search criteria
    $isAdmin = true;
}
```

You can create groups and assign them to accounts using the Stormpath
web console, or programmatically. Groups are created on directories:

```php
$group = \Stormpath\Resource\Group::instantiate(
    array('name' => 'Admins', 'description' => 'Admins Group'));

// Or
$group = $client->dataStore->instantiate(\Stormpath\Stormpath::GROUP);
$group->name = 'Admins';
$group->description = 'Admins Group';

$directory->createGroup($group);
```

Group membership can be created by:

* Explicitly creating a group membership from its resource class:

  ```php
  // the $account and $group variables must be actual
  // resource instances of their corresponding types
  $groupMembership = \Stormpath\Resource\GroupMembership::create(
    array('account' => $account, 'group' => $group));
  ```

* Using the <code>addGroup</code> method on the account instance:

  ```php
  $account->addGroup($group);
  ```

* Using the <code>addAccount</code> method on the group instance:

  ```php
  $group->addAccount($account);
  ```

## Run the tests

In order to run the tests you need to clone the repository, install the dependencies via composer and configure the api key file location. These
tests require that your computer is able to communicate with the Stormpath REST API, as they perform requests to the Stormpath servers.

To perform the installation:

    git clone https://github.com/stormpath/stormpath-sdk-php.git
    cd stormpath-sdk-php && curl -s http://getcomposer.org/installer | php && ./composer.phar install --dev

Now configure the api key file location and run the tests:

    export STORMPATH_SDK_TEST_API_KEY_FILE_LOCATION=/path/to/file/apiKey.properties
    vendor/bin/phpunit

Alternatively, configure the api key id and secret and run the tests:

    export STORMPATH_SDK_TEST_API_KEY_ID=API_KEY_ID_VALUE
    export STORMPATH_SDK_TEST_API_KEY_SECRET=API_KEY_SECRET_VALUE
    vendor/bin/phpunit

After running the tests, find the code coverage information
in the following directory: <code>tests/code-coverage</code>

## Contributing

You can make your own contributions by forking the <code>dev</code>
branch, making your changes, and issuing pull-requests on the
<code>dev</code> branch.

## Quick Class Diagram

```
+-------------+
| Application |
|             |
+-------------+
       + 1
       |
       |        +------------------------+
       |        |     AccountStore       |
       o- - - - |                        |
       |        +------------------------+
       |                     ^ 1..*
       |                     |
       |                     |
       |          +---------OR---------+
       |          |                    |
       |          |                    |
       v 0..*   1 +                    + 1
+---------------------+            +--------------+
|      Directory      | 1        1 |    Group     |1
|                     |<----------+|              |+----------+
|                     |            |              |           |
|                     | 1     0..* |              |0..*       |
|                     |+---------->|              |<-----+    |
|                     |            +--------------+      |    |         +-----------------+
|                     |                                  |    |         | GroupMembership |
|                     |                                  o- - o - - - - |                 |
|                     |            +--------------+      |    |         +-----------------+
|                     | 1     0..* |   Account    |1     |    |
|                     |+---------->|              |+-----+    |
|                     |            |              |           |
|                     | 1        1 |              |0..*       |
|                     |<----------+|              |<----------+
+---------------------+            +--------------+
```

## Copyright & Licensing

Copyright &copy; 2013 Stormpath, Inc. and contributors.

This project is licensed under the [Apache 2.0 Open Source License](http://www.apache.org/licenses/LICENSE-2.0).

For additional information, please see the full [Project Documentation](http://docs.stormpath.com/php/product-guide).

  [stormpath]: http://stormpath.com/
  [stormpath-packagist]: http://packagist.org/packages/stormpath/sdk
  [create-api-keys]: http://docs.stormpath.com/php/quickstart/#-get-an-api-key
  [stormpath-admin-login]: http://api.stormpath.com/login
  [concepts]: http://docs.stormpath.com/php/product-guide/#glossary-of-terms
  [sdk-zip]: https://github.com/stormpath/stormpath-sdk-php/archive/master.zip
  [guzzle-installation-pear]: http://guzzle.readthedocs.org/en/latest/overview.html#installation
