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
        "stormpath/sdk": "1.10.*@beta"
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
    > **Note:**
    
    > The `$applicationHref` and `$directoryHref` can be accessed from the Stormpath Administrator Console or retrieved from the code.
    > When you iterate over the object during step 3, you can output the href for the individual objects (eg `print $app->href;`)

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

### Authentication Scheme Configuration
 
You can choose one of two authentication schemes to authenticate with Stormpath:
 
 - Stormpath SAuthc1 Authentication: This is the recommended approach, and the default setting. This 
 approach computes a cryptographic digest of the request and sends the digest value along with the 
 request. If the transmitted digest matches what the Stormpath API server computes for the same 
 request, the request is authenticated. The Stormpath SAuthc1 digest-based authentication 
 scheme is more secure than standard HTTP digest authentication.
 
 - Basic Authentication: This is only recommended when your application runs in an environment outside 
 of your control, and that environment manipulates your application’s request headers when requests 
 are made. Google App Engine is one known such environment. However, Basic Authentication is not 
 as secure as Stormpath’s SAuthc algorithm, so only use this if you are forced to do so by your 
 application runtime environement.
 
When no authentication scheme is explicitly configured, Sauthc1 is used by default.
 
If you must change to basic authentication for these special environments, set the authenticationScheme 
property using Stormpath::BASIC_AUTHENTICATION_SCHEME or Stormpath::SAUTHC1_AUTHENTICATION_SCHEME:

```php
     \Stormpath\Client::$authenticationScheme = Stormpath::BASIC_AUTHENTICATION_SCHEME;
```
OR
 
```php
      $builder = new \Stormpath\ClientBuilder();
      $client = $builder->setAuthenticationScheme(Stormpath::BASIC_AUTHENTICATION_SCHEME)
                     ->build();
```


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
  
  
### ID Site
ID Site allows you to easily add authentication to your application.  It is also very easy to use.  
To use ID Site, You need to use the SDK to generate a url with a JWT.  This is very simple but you want to make sure your
application does not generate that on the page with the login link but rather a transfer thru page that generates the link 
then redirects the user to the ID Site URL. 



#### ID Site Login
As an example, you have a link on your page for Login which goes go login.php.  Your login.php file would include the following
code which generates the JWT URL

```php
$application = \Stormpath\Resource\Application::get('{APPLICATION_ID}');	
$loginLink = $application->createIdSiteUrl(['callbackUri'=>'{CALLBACK_URI}']);
header('Location:'.$loginLink);  //or any other form of redirect to the $loginLink you want to use.
```

That is all you will need for generating the login link

> NOTE:
> In order for you to be directed back to the Callback URL, you need to make sure you are explicit in the Stormpath
> Dashboard.  Include the full url on the [ID Site settings page](https://api.stormpath.com/ui2/index.html#/id-site)

For the example above, you would replace `{APPLICATION_ID}` with the id for the applicaiton you want to allow a user
to sign in to.  You then replace `{CALLBACK_URI}` with the url you want to handle the ID Site information.  

#### Handle ID Site Callback
For any request you make for ID Site, you need to specify a callback uri.  This is where the logic is stored for any
information you want to receive from the JWT about the logged in user.  To do this and get the response back from the 
Stormpath servers, you call the method handleIdSite on the application object while passing in the full Request URI
 
```php
$application = \Stormpath\Resource\Application::get('{APPLICATION_ID}');	
$response = $application->handleIdSiteCallback($_SERVER['REQUEST_URI']);	
```

> NOTE:
> A JWT Response Token can only be used once.  This is to prevent replay attacks.  It will also only be valid for a total
> of 60 seconds.  After which time, You will need to restart the workflow you were in.

#### Other ID Site Options
There are a few other methods that you will need to concern yourself with when using ID Site.  Logging out a User, 
Registering a User, and a User who has forgotten their password.  These methods will use the same information from 
the login method but a few more items will need to be passed into the array.

Logging Out a User
```php
$application = \Stormpath\Resource\Application::get('{APPLICATION_ID}');
$logoutLink = $application->createIdSiteUrl(['logout'=>true, 'callbackUri'=>'{CALLBACK_URI}']);
header('Location:'.$logoutLink);  //or any other form of redirect to the $loginLink you want to use.
```

Registering a User
```php
$application = \Stormpath\Resource\Application::get('{APPLICATION_ID}');
$registerLink = $application->createIdSiteUrl(['path'=>'/#/register','callbackUri'=>'{CALLBACK_URI}']);
header('Location:'.$registerLink);  //or any other form of redirect to the $loginLink you want to use.
```

Forgot Link
```php
$application = \Stormpath\Resource\Application::get('{APPLICATION_ID}');
$forgotLink = $application->createIdSiteUrl(['path'=>'/#/forgot','callbackUri'=>'{CALLBACK_URI}']);
header('Location:'.$forgotLink);  //or any other form of redirect to the $loginLink you want to use.
```

Again, with all these methods, You will want your application to link to an internal page where the JWT is created at 
that time.  Without doing this, a user will only have 60 seconds to click on the link before the JWT expires.

> NOTE:
> A JWT will expire after 60 seconds of creation.

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
  
### Password Import
Stormpath now allows the importing of existing passwords.  Using the [modular crypt format (MCF)][mcf]
you can now import all your existing users over to stormpath accounts.  Users must have a new account
created with a password set in one of the two support mcf formats.  To learn more about these formats, 
visit [the password import product guide][password-import-product-guide]

To use this feature, during the account creation, just pass a second parameter to the `create` method.

```php
    $account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
                                                                  'middleName' => 'Middle Name',
                                                                  'surname' => 'Surname',
                                                                  'username' => $username,
                                                                  'email' => md5(time().microtime().uniqid()) .'@unknown123.kot',
                                                                  'password' => '$2a$08$VbNS17zvQNYtMyfRiYXxWuec2F2y3SuLB/e7hU8RWdcCxxluUB3m.'));

        self::$application->createAccount($account, array('passwordFormat'=>'mcf'));
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

### Verify an Account's email address

This workflow allows you to send a welcome email to a newly registered account and optionally verify that they own the 
email addressed used during registration.

The email verification workflow involves changes to an account at an application level, and as such, this workflow
relies on the account resource as a starting point. This workflow is disabled by default for accounts, but you can 
enable it one of two ways.  The first way is to log into the Admin Console UI. Refer to the Stormpath Admin Console 
product guide for complete instructions on how to do this.  The second way is via the SDK.  

After creating or getting a directory, you will have access to the `accountCreationPolicy` object.

```php
    $directory = \Stormpath\Resource\Group::get($directoryHref);
    $accountCreationPolicy = $directory->accountCreationPolicy;
```

You can then interact with the policy resource like any other resource and set the status with either `Stormpath::ENABLED`
or `Stormpath::DISABLED`

```php
     $accountCreationPolicy->verificationEmailStatus = Stormpath::ENABLED;
     $accountCreationPolicy->verificationSuccessEmailStatus = Stormpath::ENABLED;
     $accountCreationPolicy->welcomeEmailStatus = Stormpath::ENABLED;
     
     $accountCreationPolicy->save();
```

#### Resending the verification email

In some cases, it may be needed to resend the verification email. This could be because the user accidentally deleted
the verification email or it was undeliverable at a certain time. An Application has the ability to resend verification
emails based on the account’s username or email.

##### Resend Email Verification Resource Attributes

* `login` : Either the email or username for the account that needs an email verification resent
* `accountStore` : An optional link to the application’s `accountStore` (directory or group) that you are certain contains the account attempting to resend the verification email to.

##### Execute Email Verification Resend

```
$application->sendVerificationEmail('some.user@email.com');
```

If the verification email is queued to be sent, a `202 ACCEPTED` response is returned by the server. However, the
`sendVerificationEmail` method does not return any value.

Alternatively, it is possible to specify the `accountStore` where the user account that needs verification resides as a 
performance enhancement.

```
$directory = \Stormpath\Resource\Directory::get('https://api.stormpath.com/v1/directories/2k1eykEKqVok365Ue2Y2T1');
$application->sendVerificationEmail('some.user@email.com', array('accountStore' => $directory));
```

### Password Reset

A password reset workflow, if configured on the directory the account is
registered on, can be kicked off with the
<code>sendPasswordResetEmail</code> method on an application:

```php
// this method returns the account
$account = $application->sendPasswordResetEmail('super_unique_email@unknown123.kot');
```

Alternatively, if you know the account store where the account matching the 
specified email address resides, you can include it as part of the `$options` array
in the call to <code>sendPasswordResetEmail</code>. This is useful as a performance 
enhancement if the application might be mapped to many (dozens, hundreds or 
thousands) of account stores, a common scenario in multi-tenant applications.

```php
$accountStore = $anAccountStoreMapping->getAccountStore();
// this method returns the account
$account = $application->sendPasswordResetEmail('super_unique_email@unknown123.kot', 
    array('accountStore' => $accountStore);
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

### Integrating with Google

Stormpath supports accessing accounts from a number of different 
locations including Google. Google uses OAuth 2.0 protocol for 
authentication / authorization and Stormpath can leverage their 
authorization code (or access tokens) to return an `Account` for 
a given code.

The steps to enable this functionality into your application include:

+ Create a Google Directory
+ Create an `AccountStoreMapping` between a Google Directory and your `Application`
+ Accessing Accounts with Google Authorization Codes or Access Tokens

Google Directories follow behavior similar to mirror directories, but 
have a `Provider` resource that contains information regarding the Google 
application that the directory is configured for.

#### Google Provider Resource

A GoogleProvider resource holds specific information needed for working with 
a Google Directory. It is important to understand the format of the provider 
resource when creating and updating a Google Directory.

A provider resource can be obtained by accessing the directory’s provider as 
follows:

```PHP
$provider = $client->dataStore->getResource("https://api.stormpath.com/v1/directories/1mBDmVgW8JEon4AkoSYjPv/provider",
    \Stormpath\Stormpath::GOOGLE_PROVIDER);
```

or, by means of the directory `Resource`:

```PHP
$provider = $directory->getProvider();
```

Alternatively, it is possible to use the static client configuration to the get the `Provider`:

```PHP
// It is also possible to specify the URL ending with "/provider", 
// or the partial path (which could be "directories/DIR_ID/provider", 
// or "DIR_ID/provider" or just "DIR_ID"). 
$directoryHref = "https://api.stormpath.com/v1/directories/1mBDmVgW8JEon4AkoSYjPv"; 
$provider = GoogleProvider::get($directoryHref);
```

##### Resource Attributes

* `clientId` : The App ID for your Google application
* `clientSecret` : The App Secret for your Google application
* `redirectUri` : The redirection Uri for your Google application
* `providerId` : The provider ID is the Stormpath ID for the Directory’s account provider

In addition to your application specific attributes, a Provider resource 
will always contain 3 reserved read-only fields:

* `href` : The fully qualified location of the custom data resource
* `createdAt` : the UTC timestamp with millisecond precision of when the resource was created in Stormpath as an ISO 8601 formatted string
* `modifiedAt` : the UTC timestamp with millisecond precision of when the resource was created in Stormpath as an ISO 8601 formatted string

#### Creating a Google Directory

Creating a Google Directory requires that you gather some information 
beforehand from Google’s Developer Console regarding your application.

* Client ID
* Client Secret
* Redirect URI

Creating a Google Directory is very similar to creating a directory within 
Stormpath. For a Google Directory to be configured correctly, you must 
specify the correct Provider information.

```PHP
$provider = $client->dataStore->instantiate(\Stormpath\Stormpath::GOOGLE_PROVIDER);
$provider->clientId = "857385-m8vk0fn2r7jmjo.apps.googleusercontent.com";
$provider->clientSecret = "ehs7_-bA7OWQSQ4";
$provider->redirectUri = "https://myapplication.com/authenticate";

$directory = $client->dataStore->instantiate(\Stormpath\Stormpath::DIRECTORY);
$directory->name = "my-google-directory";
$directory->description = "A Google directory";
$directory->provider = $provider;

$tenant = $client->getCurrentTenant();
$directory = $tenant->createDirectory($directory);
```

After the Google Directory has been created, it needs to be mapped with an 
application as an account store. The Google Directory cannot be a default 
account store or a default group store. Once the directory is mapped as an 
account store for an application, you are ready to access Accounts with 
Google Authorization Codes.

#### Accessing Accounts with Google Authorization Codes or Access Tokens

To access or create an account in an already created Google Directory, it is 
required to gather a Google Authorization Code on behalf of the user. This 
requires leveraging Google’s OAuth 2.0 protocol and the user’s consent for 
your application’s permissions.

Once the Authorization Code is gathered, you can get or create the `Account` by 
means of the `Application` and specifying a `ProviderRequest`. The following example 
shows how you use `GoogleProviderAccountRequest` to get an `Account` for a given authorization code:

```PHP
$applicationHref = "https://api.stormpath.com/v1/applications/24mp4us71ntza6lBwlu";
$application = $client->getResource(applicationHref, \Stormpath\Stormpath::APPLICATION);
$providerRequest = new GoogleProviderAccountRequest(array(
    "code" => "4/a3p_fn0sMDQlyFVTYwfl5GAj0Obd.oiruVLbQZSwU3oEBd8DOtNApQzTCiwI"
));

$result = $application->getAccount($providerRequest);
$account = $result->getAccount();
```

In order to know if the account was created or if it already existed in the 
Stormpath’s Google Directory you can do: `result.isNewAccount();`. It will return 
`true` if it is a newly created account; `false` otherwise.

Once an `Account` is retrieved, Stormpath maps common fields for the Google User to 
the `Account`. The access token and the refresh token for any additional calls in the 
`GoogleProviderData` resource and can be retrieved by:

```PHP
$providerData = $account->getProviderData();
```

The returned GoogleProviderData includes:

```PHP
$providerData->accessToken; //-> y29.1.AADN_Xo2hxQflWwsgCSK-WjSw1mNfZiv4
$providerData->createdAt; //-> 2014-04-01T17:00:09.154Z 
$providerData->href; //-> https://api.stormpath.com/v1/accounts/ciYmtETytH0tbHRBas1D5/providerData 
$providerData->modifiedAt; //-> 2014-04-01T17:00:09.189Z 
$providerData->providerId; //-> google 
$providerData->refreshToken; //-> 1/qQTS638g3ArE4U02FoiXL1yIh-OiPmhc
```

The `accessToken` can also be passed as a field for the `ProviderData` to access the 
account once it is retrieved:

```PHP
$providerRequest = new GoogleProviderRequest(array(
    "accessToken" =>"y29.1.AADN_Xo2hxQflWwsgCSK-WjSw1mNfZiv4"
));
$result = $application->getAccount(request);
$account = $result->getAccount();
```

The refreshToken will only be present if your application asked for offline access. 
Review Google’s documentation for more information regarding OAuth offline access.

### Integrating with Facebook

Stormpath supports accessing accounts from a number of different locations including 
Facebook. Facebook uses OAuth 2.0 protocol for authentication / authorization and 
Stormpath can leverage their or access tokens to return an Account for a given code.

The steps to enable this functionality into your application include:

* Create a Facebook Directory
* Create an Account Store Mapping between a Facebook Directory and your Application
* Accessing Accounts with Facebook User Access Tokens

Facebook Directories follow behavior similar to mirror directories, but have a
Provider resource that contains information regarding the Facebook application that
the directory is configured for.

#### FACEBOOK PROVIDER RESOURCE

A FacebookProvider resource holds specific information needed for working with a 
Facebook Directory. It is important to understand the format of the provider resource 
when creating and updating a Facebook Directory.

A provider resource can be obtained by accessing the directory’s provider as follows:

Example Request

```PHP
$provider = $client->dataStore->getResource("https://api.stormpath.com/v1/directories/72N2MjJSIXuln56sNngcvr/provider",
    \Stormpath\Stormpath::FACEBOOK_PROVIDER);
```

or, by means of the directory Resource:

```PHP
$provider = $directory->getProvider();
```

Alternatively, it is possible to use the static client configuration to the get the `Provider`:

```PHP
// It is also possible to specify the URL ending with "/provider", 
// or the partial path (which could be "directories/DIR_ID/provider", 
// or "DIR_ID/provider" or just "DIR_ID"). 
$directoryHref = "https://api.stormpath.com/v1/directories/1mBDmVgW8JEon4AkoSYjPv"; 
$provider = FacebookProvider::get($directoryHref);
```

##### Resource Attributes

* `clientId` : The App ID for your Facebook application
* `clientSecret` : The App Secret for your Facebook application
* `providerId` : The provider ID is the Stormpath ID for the Directory’s account provider

In addition to your application specific attributes, a Provider resource will always contain 3 reserved read-only fields:

* `href` : The fully qualified location of the custom data resource
* `createdAt` : the UTC timestamp with millisecond precision of when the resource was created in Stormpath as an ISO 8601 formatted string
* `modifiedAt` : the UTC timestamp with millisecond precision of when the resource was created in Stormpath as an ISO 8601 formatted string

#### CREATING A FACEBOOK DIRECTORY

Creating a Facebook Directory requires that you gather some information beforehand 
from Facebook’s Developer Console regarding your application.

* Client ID
* Client Secret

Creating a Facebook Directory is very similar to creating a directory within Stormpath.
 For a Facebook Directory to be configured correctly, you must specify the correct 
 Provider information.

Example Request

```
$provider = $client->dataStore->instantiate(\Stormpath\Stormpath::FACEBOOK_PROVIDER);
$provider->clientId = "1011854538839621";
$provider->clientSecret = "82c16954b0d88216127d66ac44bbc3a8";
$provider->redirectUri = "https://apps.facebook.com/sampleapp";

$directory = $client->dataStore->instantiate(\Stormpath\Stormpath::DIRECTORY);
$directory->name = "my-fb-directory";
$directory->description = "A Facebook directory";
$directory->provider = $provider;

$tenant = $client->getCurrentTenant();
$directory = $tenant->createDirectory($directory);
```

After the Facebook Directory has been created, it needs to be mapped with an 
application as an account store. The Facebook Directory cannot be a default account 
store or a default group store. Once the directory is mapped to an account store for 
an application, you are ready to access Accounts with Facebook User Access Tokens.

#### ACCESSING ACCOUNTS WITH FACEBOOK USER ACCESS TOKENS
To access or create an account in an already created Facebook Directory, it is 
required to gather the User Access Token on behalf of the user. This usually requires 
leveraging Facebook’s javascript library and the user’s consent for your application’s 
permissions.

It is required that your Facebook application request for the email permission from 
Facebook. If the access token does not grant email permissions, you will not be able 
to get an Account with an access token.

Once the Authorization Code is gathered, you can get or create the `Account` by means of 
the `Application` and specifying its `ProviderData`. The following example shows how you 
use `FacebookProviderAccountRequest` to get an `Account` for a given authorization code:

Example Request

```PHP
$applicationHref = "https://api.stormpath.com/v1/applications/2k1aegw9UbLX4ZfMH4kCkR";
$application = \Stormpath\Resource\Application::get($applicationHref);

$providerAccountRequest = new \Stormpath\Provider\FacebookProviderAccountRequest(array(
    "accessToken" => "CABTmZxAZBxBADbr1l7ZCwHpjivBt9T0GZBqjQdTmgyO0OkUq37HYaBi4F23f49f5"
));

$result = $application->getAccount($providerRequest);
$account = $result->getAccount();
```

In order to know if the account was created or if it already existed in the 
Stormpath’s Facebook Directory you can do: `$result->isNewAccount();`. It will return 
`true` if it is a newly created account; `false` otherwise.

Once an `Account` is retrieved, Stormpath maps common fields for the Facebook User to 
the `Account`. The access token for any additional calls in the `FacebookProviderData` 
resource and can be retrieved by:

```PHP
$providerData = $account->getProviderData();
```
The returned `FacebookProviderData` will include:

```PHP
$providerData->accessToken; //-> CABTmZxAZBxBADbr1l7ZCwHpjivBt9T0GZBqjQdTmgyO0OkUq37HYaBi4F23f49f5
$providerData->createdAt; //-> 2014-04-01T17:00:09.154Z
$providerData->href; //-> https://api.stormpath.com/v1/accounts/ciYmtETytH0tbHRBas1D5/providerData
$providerData->modifiedAt; //-> 2014-04-01T17:00:09.189Z
$providerData->providerId; //-> facebook
```

## Using Stormpath for API Authentication

### Create an Account for your developers

First, you will need user accounts in Stormpath to represent the people that are developing against 
your API. Accounts can not only represent Developers, but also can be used to represent services, 
daemons, processes, or any “entity” that needs to login to a Stormpath-secured API.

```php
$account = \Stormpath\Resource\Account::instantiate(
  array('givenName' => 'Joe',
        'surname' => 'Stormtrooper',
        'email' => 'tk421@stormpath.com',
        'password' => 'Changeme1'));

$application->createAccount($account);
```

### Create and Manage API Keys for an Account

After you create an account for a developer, you will need to generate an API Key (or multiple) 
to be used when accessing your API. Each account will have an `apiKeys` property that contains a 
collection of their API Keys. There will also be a list of API keys on a account’s profile in the 
Stormpath Admin Console. You will be able to both create and manage keys in both.

#### CREATING API KEYS FOR AN ACCOUNT 

```php
$apiKey = $account->createApiKey();

$apiKeyId = $apikey->id;
$apiKeySecret = $apikey->secret;
```

The `ApiKey` returned will have the following properties:
* id	    The unique identifier for the API Key
* secret	The secret for the API key.
* status	A property that represent the status of the key. Keys with a disabled status will not be able to authenticate.
* account	A link to the ApiKey’s account.
* tenant	A link to the ApiKey’s tenant.

#### MANAGE API KEYS FOR AN ACCOUNT

##### Deleting an API Key

```php
$apiKeyId = 'FURThLWDE4MElDTFVUMDNDTzpQSHozZ';
$apiKey = $application->getApiKey($apiKeyId);
$apiKey->delete();
```

##### Disable an API key

```php
$apiKeyId = 'FURThLWDE4MElDTFVUMDNDTzpQSHozZ' 
$apiKey = $application->getApiKey('FURThLWDE4MElDTFVUMDNDTzpQSHozZ');
$apiKey->status = 'DISABLED';
$apiKey->save()
```

### Using the Stormpath SDK to Authenticate and Generate Tokens for your API Keys

The class `ApiRequestAuthenticator` can be used to perform both Basic Authentication 
and OAuth Authentication.
 
### Basic Authentication

```
GET /troopers/tk421/equipment 
Accept: application/json
Authorization: Basic MzRVU1BWVUFURThLWDE4MElDTFVUMDNDTzpQSHozZitnMzNiNFpHc1R3dEtOQ2h0NzhBejNpSjdwWTIwREo5N0R2L1g4
Host: api.trooperapp.com
```

The `Authorization` header contains a base64 encoding of the API Key and Secret.

#### Using `ApiRequestAuthenticator`

```php
$application = \Stormpath\Resource\Application::get("https://api.stormpath.com/v1/applications/24mp4us71ntza6lBwlu");

$request = \Stormpath\Authc\Api\Request::createFromGlobals();
$result = new ApiRequestAuthenticator($application)->authenticate($request);

$account = $result->account;
$apiKey = $result->apiKey;
```

##### Using `BasicRequestAuthenticator`

```php
$application = \Stormpath\Resource\Application::get("https://api.stormpath.com/v1/applications/24mp4us71ntza6lBwlu");

$request = \Stormpath\Authc\Api\Request::createFromGlobals();
$result = new BasicRequestAuthenticator($application)->authenticate($request);

$account = $result->account;
$apiKey = $result->apiKey;
```

### OAuth Authentication

#### GENERATING A TOKEN

```
POST /oauth/token
Accept: application/json
Authorization: Basic MzRVU1BWVUFURThLWDE4MElDTFVUMDNDTzpQSHozZitnMzNiNFpHc1
Content-Type: application/x-www-form-urlencoded
Host: api.trooperapp.com

  grant_type=client_credentials
```

The `Authorization` header contains a base64 encoding of the API Key and Secret.

```php
$application = \Stormpath\Resource\Application::get("https://api.stormpath.com/v1/applications/24mp4us71ntza6lBwlu");

$request = \Stormpath\Authc\Api\Request::createFromGlobals();
$result = new ApiRequestAuthenticator($application)->authenticate($request);

$tokenResponse = $result->tokenResponse;
$token = $tokenResponse->accessToken;
$json = $tokenResponse->toJson();
```

Alternatively, it's possible to use `OAuthRequestAuthenticator` or the more specific 
authenticator `OAuthClientCredentialsRequestAuthenticator` to generate access tokens.

The response including the access token looks like this: 

```
HTTP 200 OK
Content-Type: application/json

{
   "access_token":"7FRhtCNRapj9zs.YI8MqPiS8hzx3wJH4.qT29JUOpU64T",
   "token_type":"bearer",
   "expires_in":3600
}
```

#### AUTHENTICATION USING TOKEN

```
GET /troopers/tk421/equipment 
Accept: application/json
Authorization: Bearer 7FRhtCNRapj9zs.YI8MqPiS8hzx3wJH4.qT29JUOpU64T
Host: api.trooperapp.com
```

```php
$application = \Stormpath\Resource\Application::get("https://api.stormpath.com/v1/applications/24mp4us71ntza6lBwlu");

$request = \Stormpath\Authc\Api\Request::createFromGlobals();
$result = new OAuthRequestAuthenticator($application)->authenticate($request);

$account = $result->account;
$apiKey = $result->apiKey;
```

You can also use the more specific `OAuthBearerRequestAuthenticator` to authenticate 
token access requests.

### Password and Refresh Token Grant types
For details on Token Management and authenticating with Password Access Tokens and Refreshing the access tokens,
View our [Stormpath Token Management Product Guide][token-management]

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
  [mcf]: https://pythonhosted.org/passlib/modular_crypt_format.html
  [password-import-product-guide]: https://pythonhosted.org/passlib/modular_crypt_format.html
  [token-management]: http://docs.stormpath.com/guides/token-management/
