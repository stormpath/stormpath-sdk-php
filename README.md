# Stormpath PHP SDK
Copyright &copy; 2013 Stormpath, Inc. and contributors.

This project is licensed under the [Apache 2.0 Open Source License](http://www.apache.org/licenses/LICENSE-2.0).

For additional information, please see the full [Project Documentation](https://www.stormpath.com/docs/php/product-guide).

## Installation

You can install **stormpath-sdk-php** via Composer or by downloading the source.

### Via Composer:

**stormpath-sdk-php** is available on Packagist as the 
[`stormpath/sdk`](http://packagist.org/packages/stormpath/sdk) package.

You will need to include the PEAR repository to your **composer.json** file and, of course, the **stormpath/sdk** dependency:

    "repositories": [
        {
            "type": "pear",
            "url": "http://pear.php.net"
        }
    ],"require": {
        "stormpath/sdk": "*"
    }

Once the project and its dependencies have been installed, include the composer auto loader file:

    require 'vendor/autoload.php';
    
### Download the source code

Go to the [tags directory](https://github.com/stormpath/stormpath-sdk-php/tags) and download the latest version of the SDK which includes all
dependencies, with the exception of HTTP_Request2.

The HTTP_Request2 dependency must be installed via PEAR (version 2.1.1 is the minimum required). 
[Click here to visit the HTTP_Request2 PEAR website.](http://pear.php.net/package/HTTP_Request2/)

Once you download the library, move the stormpath-sdk-php folder to your project
directory and then include the library file:

    require '/path/to/stormpath-sdk-php/Services/Stormpath.php';

You're ready to connect with Stormpath!

## Documentation

For additional information, please see the full [Project Documentation](https://www.stormpath.com/docs/php/product-guide).
