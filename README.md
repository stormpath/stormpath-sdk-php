[![Build Status](https://api.travis-ci.org/stormpath/stormpath-sdk-php.png)](https://travis-ci.org/stormpath/stormpath-sdk-php)

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
        "stormpath/sdk": "~ 1.0.*@beta"
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

        $application->authenticate('johnsmith', '4P@$$w0rd!');

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

### Collections
#### Search

Resource collections can be searched by a general query string, or by attribute or by specifying a Search object.

Passing a string to the search method will filter by any attribute on the collection:

  ```php
  $tenant->applications->search = 'foo';

  //Or

  $search = new \Stormpath\Resource\Search();
  $search->filter = 'foo';
  $tenant->applications->search = $search;
  ```

To search a specific attribute or attributes, pass an array:

  ```php
  $tenant->applications->search = array('name' => '*foo*',
                                        'description' => 'bar*',
                                        'status' => 'enabled');

  //Or

  $search = new \Stormpath\Resource\Search();
  $tenant->applications->search = $search->addMatchAnywhere('name', 'foo')->
                                           addStartsWith('description', 'bar')->
                                           addEquals('status', 'enabled');
  ```
Now you can loop throw the collection resource and get the results according to the specified search:

  ```php
  foreach($tenant->applications as $app)
  {
    print $app->name;
  }
  ```

Alternatively, you can use the collection getter options to specify the search:

  ```php
  $applications = $tenant->getApplications(array('q' => 'foo'));
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
  $tenant->applications->order = new \Stormpath\Resource\Order(array('name'), 'desc');
  ```

Or specify the order by string:

  ```php
  $tenant->applications->order = 'name desc';
  ```

Now you can loop throw the collection resource and get the results according to the specified order:

  ```php
  foreach($tenant->applications as $app)
  {
    print $app->name;
  }
  ```

Alternatively, you can use the collection getter options to specify the order:

  ```php
  $applications = $tenant->getApplications(array('orderBy' => 'name desc'));
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
  [guzzle-installation-pear]: http://guzzlephp.org/getting-started/installation.html#pear
