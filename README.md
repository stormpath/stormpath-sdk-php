stormpath-sdk-php
=================

PHP SDK for the Stormpath Identity and Access Manager REST+JSON API

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
        "stormpath/sdk": "0.1.0"
    }

### Download the source code

[Click here to download the source
(.zip)](https://github.com/stormpath/stormpath-sdk-php/zipball/master) which includes all
dependencies, with the exception of HTTP_Request2.

The HTTP_Request2 dependency must be installed via PEAR (version 2.1.1 is the minimum required). 
[Click here to visit the HTTP_Request2 PEAR website.](http://pear.php.net/package/HTTP_Request2/)

Once you download the library, move the stormpath-sdk-php folder to your project
directory and then include the library file:

    require '/path/to/stormpath-sdk-php/Services/Stormpath.php';

You're ready to connect with Stormpath!

## Documentation

Project Documentation is here:

https://github.com/stormpath/stormpath-sdk-php/wiki