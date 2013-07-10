# Stormpath Client for PHP

For additional information, please see the full [Project Documentation](https://www.stormpath.com/docs/php/product-guide).

[![Build Status](https://travis-ci.org/TomHAnderson/stormpath-sdk-php.png)](https://travis-ci.org/TomHAnderson/stormpath-sdk-php)

Installation
------------
  1. edit `composer.json` file with following contents:

     ```json
     "require": {
        "stormpath/stormpath-tha": "dev-master"
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

To fetch a new page of results from a collection clear the collection and set the new limit and/or offset.  The collection will lazy load the next time it is used.

```php
    $groupsCollection->clear();
    $groupsCollection->setOffset(25);
    $this->assertEquals(25, sizeof($groupsCollection));

    $groupsCollection->clear();
    $groupsCollection->setLimit(5);
    $groupsCollection->setOffset(0);
    $this->assertEquals(5, sizeof($groupsCollection));
```


Finding Resources
-----------------

To find an existing resource use the find() method of the Resource Manager.

```php
    $resourceManager = StormpathService::getResourceManager();
    
    // Parameters are the Resource class and id for the resource
    $account = $resourceManager->find('Stormpath\Resource\Account', $resourceId);
```

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
    use Stormpath\Service\StormpathService as Stormpath;

    Stormpath::configure($id, $secret);
    Stormpath::register('New Name', 'Description', 'enabled'); 
```

Optionally enable Basic authentication instead of default Digest

```php
    use Zend\Http\Client;
    use Stormpath\Http\Client\Adapter\Basic;

    Stormpath::configure($id, $secret);
    $client = new Client();
    $adapter = new Basic();
    $client->setAdapter($adapter);
    Stormpath::setHttpClient($client);
```

Optionally enable apc cache

```
    use Zend\Cache\StorageFactory;

    Stormpath::setCache(StorageFactory::adapterFactory('apc'));
```


Common Resource Properties
--------------------------

Every resource shares these methods

```php
    // Return the id
    $resource->getId(); 
    
    // Return the Href including the id portion
    $resource->getHref();
```


Account
-------

Accounts must be created against an Application or a Directory.  To specify which just set the property of either using ``` $account->setApplication($application); ``` or ``` $account->setDirectory($directory); ```

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

Editable setters ``` $account->set[Setter]($value); ``` are

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

Editable setters are

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

Editable setters are

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

Editable setters are

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

Editable setters are Resources.  Group Memberships may only be created or deleted.  To create a Group Membership set the Account and Group then persist.

Setters used when created
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

TODO


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


This project is licensed under the [Apache 2.0 Open Source License](http://www.apache.org/licenses/LICENSE-2.0).

Copyright &copy; 2013 Stormpath, Inc. and contributors.  
