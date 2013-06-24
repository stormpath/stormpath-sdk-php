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
Create a client through the service
```php
use Stormpath\Service\StormpathService as Stormpath;

Stormpath::CreateClient($accessId, $secretId);
```

This project is licensed under the [Apache 2.0 Open Source License](http://www.apache.org/licenses/LICENSE-2.0).

Copyright &copy; 2013 Stormpath, Inc. and contributors.  
