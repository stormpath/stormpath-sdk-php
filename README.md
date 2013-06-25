# Stormpath SDK for PHP

For additional information, please see the full [Project Documentation](https://www.stormpath.com/docs/php/product-guide).

Installation
------------
  1. edit `composer.json` file with following contents:

     ```json
     "require": {
        "stormpath/stormpath": "dev-master"
     }
     ```
  2. install composer via `curl -s http://getcomposer.org/installer | php` (on windows, download
     http://getcomposer.org/installer and execute it with PHP)
  3. run `php composer.phar install`

    
Use
---
Configure a new application
```
Stormpath::configure($id, $secret);
Stormpath::register('New Name', 'Description', 'enabled'); 
```

Enable Basic authentication instead of Digest
```
use Zend\Http\Client;
use Stormpath\Http\Client\Adapter\Basic;

Stormpath::configure($id, $secret);
$client = new Client();
$adapter = new Basic();
$client->setAdapter($adapter);
Stormpath::setHttpClient($client);
```

Testing
-------
Create a ```local.php``` file and set api parameters

```
<?php
// ~/test/autoload/local.php

return [
    'stormpath' => [
        'id' => 'stormpath_id',
        'secret' => 'stormpath_secret',
    ]
];
```


This project is licensed under the [Apache 2.0 Open Source License](http://www.apache.org/licenses/LICENSE-2.0).

Copyright &copy; 2013 Stormpath, Inc. and contributors.  
