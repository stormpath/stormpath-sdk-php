# Stormpath Client for PHP

[![Build Status](https://travis-ci.org/TomHAnderson/stormpath-sdk-php.png)](https://travis-ci.org/TomHAnderson/stormpath-sdk-php)
[![Dependency Status](https://www.versioneye.com/user/projects/51e052589041060002005a07/badge.png)](https://www.versioneye.com/user/projects/51e052589041060002005a07)

Installation
------------
  1. edit `composer.json` file with following contents:

     ```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/TomHAnderson/StormpathClient-PHP"
        }
    ],
    "require": {
        "stormpath/stormpath": "dev-master"
    }
     ```
  2. install composer via `curl -s http://getcomposer.org/installer | php` (on windows, download
     http://getcomposer.org/installer and execute it with PHP)
  3. run `php composer.phar install`

Overview
========

This API client is an Object Relational Mapper.  Here, entities are called Resources.  
These are the available Resources:

1. Stormpath\Resource\Account
2. Stormpath\Resource\Application
3. Stormpath\Resource\Directory
4. Stormpath\Resource\Group
5. Stormpath\Resource\GroupMembership
6. Stormpath\Resource\LoginAttempt
7. Stormpath\Resource\PasswordResetToken
8. Stormpath\Resource\Tenant

When resources are found using find() they are initialized immediatly.  When Resources are part of a 
collection they are lazy loaded so the Resource is not fetched from the server until it is acted upon
through a getter, setter, or Resource Manager action.

The Resource Manager is a Doctrine Object Manager.  Multiple Resources may be set for insert, update, or delete
and all acted upon when the Resource Manager is flush(); ed.  To queue a Resource for addition or update use ``` $resourceManager->persist($resource); ```  To queue a Resource for deletion use ``` $resourceManager->remove($resource); ```


Collections
-----------

A collection is a group of related Resources as a property of a Resource and may be paginated.  By default collections have a limit of 25 and an offset of 0.  These may be changed by fetching the collection and using ``` $collection->setLimit(#); ``` and ``` $collection->setOffset(#); ```

```php
    $groupsCollection = $directory->getGroups();
    $this->assertEquals(25, sizeof($groupsCollection));
```

To fetch a new page of results from a collection set the new limit and/or offset.  This will clear the collection so the next time it's accessed it will be with the new offset/limit(s).  The collection will lazy load the next time it is used.

```php
    $groupsCollection->setOffset(25);
    $this->assertEquals(25, sizeof($groupsCollection));

    $groupsCollection->setLimit(5);
    $groupsCollection->setOffset(0);
    $this->assertEquals(5, sizeof($groupsCollection));
```

You may search collections by setting setSearch(string|array);  The collection is reset when the search, offset, or limit is set and will lazy load when the collection is next accessed.

```php
    // Search all properties for Joe
    $groupsCollection->setSearch('Joe');
    
    // Search name for Joe
    $groupsCollection->setSearch(array(
        'name' => 'Joe'
    ));
```

You may sort collections

```php

    $groupsCollection->setOrderBy(array('name' => 'ASC', 'description' => 'DESC')); 
```

See https://www.stormpath.com/docs/rest/api#CollectionResources for more details of search options.


Eager Loading References
------------------------

The Stormpath API documentation refers to this as Resource Expansion.  You may use Resource Expansion when using
the ``` $resourceManager->find(); ``` method.  Resource Expansion will occur for the found resource only and will not occur for resources which are returned from the find().  In other words, when a resource is fetched eagerly, with Resource Expansion, only those resources directly associated to the found Resource will be eagerly loaded.  Resources which are properties of the Resources which are eagerly loaded are not eagerly loaded.  This avoids a waterfall affect of loading whole trees of data with one request.

To eagerly load a resouce use ``` $resourceManager->find('Stormpath\Resource\ResourceName', $resourceId, true); ``` setting the third parameter to true to enable resource expansion.


Finding Resources
-----------------

To find an existing resource use the find() method of the Resource Manager.

```php
    use Stormpath\Service\StormpathService;

    $resourceManager = StormpathService::getResourceManager();
    
    // Parameters are the Resource class and id for the resource
    $account = $resourceManager->find('Stormpath\Resource\Account', $resourceId);
```

To eagerly load a resouce use ``` $resourceManager->find('Stormpath\Resource\ResourceName', $resourceId, true);


Creating a Resource
-------------------

To create a new resource create a new instance of it's resource class, assign applicable properties then persist it in the Resource Manager.

```php
    use Stormpath\Service\StormpathService;
    use Stormpath\Resource\Application;

    $resourceManager = StormpathService::getResourceManager();

    $app = new Application;
    
    $app->setName(md5(rand()));
    $app->setDescription('API Created Application');
    $app->setStatus('ENABLED');

    $resourceManager->persist($app);
    $resourceManager->flush();
```

After running this code the $app object will be a fully populated Application resource.


Editing a Resource
------------------

Editing resources is as simple as setting properties on a found object then persisting the resource.

```php
    use Stormpath\Service\StormpathService;
    
    $resourceManager = StormpathService::getResourceManager();
    
    // Parameters are the Resource class and id for the resource
    $account = $resourceManager->find('Stormpath\Resource\Account', $accountId);
    
    $account->setSurname('ChangedSurname');
    
    $resourceManager->persist($account)
    $resourceManager->flush();
```


Deleting a Resource
-------------------

Use the resource manager to delete resources

```php
    use Stormpath\Service\StormpathService;
    
    $resourceManager = StormpathService::getResourceManager();
    
    // Parameters are the Resource class and id for the resource
    $account = $resourceManager->find('Stormpath\Resource\Account', $accountId);
    
    $resourceManager->remove($account)
    $resourceManager->flush();
```


Use
---
Configure for use

```php
    use Stormpath\Service\StormpathService;

    StormpathService::configure($id, $secret);
    StormpathService::register('New Name', 'Description', 'enabled'); 
```

Optionally enable Basic authentication instead of the default Digest authentication

```php
    use Zend\Http\Client;
    use Stormpath\Http\Client\Adapter\Basic;

    StormpathService::configure($id, $secret);
    $client = new Client();
    $adapter = new Basic();
    $client->setAdapter($adapter);
    StormpathService::setHttpClient($client);
```

By default the memory cache is used.  You may enable an alternative cache.  See https://packages.zendframework.com/docs/latest/manual/en/modules/zend.cache.storage.adapter.html for all available cache adapters.  The advantage of enabling an alternative cache is the cache may persist between user sessions.

```php
    use Zend\Cache\StorageFactory;

    Stormpath::setCache(StorageFactory::adapterFactory('apc'));
```


Common Resource Properties
--------------------------

Every resource shares these methods

```php
    // Return the resource id
    $resource->getId(); 
    
    // Return the resource Href including the id portion
    $resource->getHref();
```


Account
-------

Accounts must be created against an Application or a Directory.  To specify which just set the property of either using ``` $account->setApplication($application); ``` or ``` $account->setDirectory($directory); ``` at the time you create the account resource.

Create a new account and assign it to an Application

```php
    use Stormpath\Service\StormpathService;
    use Stormpath\Resource\Account;

    $resourceManager = StormpathService::getResourceManager();

    // Parameters are the Resource class and id for the resource
    $application = $resourceManager->find('Stormpath\Resource\Application', $applicationId);

    $account = new Account;
    $account->setUsername(md5(rand()));
    $account->setEmail(md5(rand()) . '@test.stormpath.com');
    
    // Passwords must contain upper and lower case characters
    $account->setPassword(md5(rand()) . strtoupper(md5(rand())));
    $account->setGivenName('Test');
    $account->setMiddleName('User');
    $account->setSurname('One');
    $account->setStatus('ACTIVE');
    
    $account->setApplication($application);
    
    // To assign to a directory instead
    #  $account->setDirectory($directory);

    $resourceManager->persist($account);
    $resourceManager->flush();
```

Properties (editable with ``` $account->set[Property]($value); ```)

```
    Username
    Email
    Password
    GivenName
    MiddleName
    Surname
    Status
```

References

```
    Application - only used when creating an account.  This reference is not populated from a find() call.
    Directory
    Tenant
```

Collections

```
    Groups
```


Application
-----------

Create a new application

```php
    use Stormpath\Service\StormpathService;

    $resourceManager = StormpathService::getResourceManager();

    $app = new Application;

    $app->setName(md5(rand()));
    $app->setDescription('phpunit test application');
    $app->setStatus('ENABLED');

    $resourceManager->persist($app);
    $resourceManager->flush();
```

Properties

```
    Name
    Description
    Status
```

References

```
    Tenant
```

Collections

```
    Accounts
    Groups
    LoginAttempts
    PasswordResetTokens
```


Directory
---------

Properties

```
    Name
    Description
    Status
```

References

```
    Tenant
```

Collections

```
    Accounts
    Groups
```

Group
-----

You must set a Directory before persisting a new Group.

Properties

```
    Name
    Description
    Status
```

References

```
    Tenant
    Directory
```

Collections

```
    Accounts
    AccountMemberships
```


Group Membership
----------------

All properties are Resources.  Group Memberships may only be created or deleted.  To create a Group Membership set the Account and Group then persist.

Properties set when created
```
    Account
    Group
```


Login Attempt
-------------

A login attempt is the Resource to use when you want to authenticate a user by username and password against an application.  All three parameters are required.

```
    $loginAttempt = new LoginAttempt;
    $loginAttempt->setUsername($username);
    $loginAttempt->setPassword($password);
    $loginAttempt->setApplication($application);

    $resourceManager->persist($loginAttempt);

    try {
        $resourceManager->flush();
        $authorizedAccount = $loginAttempt->getAccount();
    } catch (\Exception $e) {
        // Check exception for failure code
    }
```


Password Reset Token
--------------------

To initialize a password reset email create a PasswordResetToken, set the email and application, and persist and flush it.  Post flush the PasswordResetToken will contain the acocunt which was reset.

```php
    use Stormpath\Resource\PasswordResetToken;

    $application = $resourceManager->find('Stormpath\Resource\Application', $applicationId);

    $passwordResetToken = new PasswordResetToken;
    $passwordResetTokan->setApplication($application);
    $passwordResetToken->setEmail('resetpassword@test.stormpath.com');
    $resourceManager->persist($passwordResetToken);

    $resourceManager->flush();

    $account = $passwordResetToken->getAccount();
```    


Tenant
------

Get the current tenant

```php
    $currentTenant = $resourceManager->find('Stormpath\Resource\Tenant', 'current');
```



Testing
-------
Create a ```local.php``` file and set api parameters and run composer with --dev then run phpunit.

```php
<?php
// ~/test/autoload/local.php

return array(
    'stormpath' => array(
        'id' => 'stormpath_id',
        'secret' => 'stormpath_secret',
    ),
);
```

For additional information, please see the [Product Guide](https://www.stormpath.com/docs/php/product-guide).

This project is licensed under the [Apache 2.0 Open Source License](http://www.apache.org/licenses/LICENSE-2.0).

Copyright &copy; 2013 Stormpath, Inc. and contributors.  
