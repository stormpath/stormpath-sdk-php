# Stormpath PHP SDK
Copyright &copy; 2013 Stormpath, Inc. and contributors.

This project is licensed under the [Apache 2.0 Open Source License](http://www.apache.org/licenses/LICENSE-2.0).

For additional information, please see the full [Project Documentation](http://docs.stormpath.com/php/product-guide).

## Installation

You can install **stormpath-sdk-php** via Composer or by downloading the source.

### Via Composer:

**stormpath-sdk-php** is available on Packagist as the 
[`stormpath/sdk`](http://packagist.org/packages/stormpath/sdk) package.

On your project root, install Composer

    curl -sS https://getcomposer.org/installer | php

Configure the **stormpath/sdk** dependency in your 'composer.json' file:

    "require": {
        "stormpath/sdk": "~ 1.0*@beta"
    }

On your project root, install the the SDK with its dependencies:

    php composer.phar install

Once the project and its dependencies have been installed, include the composer auto loader file:

    require 'vendor/autoload.php';
    
### Download the source code

Go to the [tags directory](https://github.com/stormpath/stormpath-sdk-php/tags) and download the latest version of the SDK which includes all
dependencies, with the exception of Guzzle (the Http client).

The Guzzle Http dependency can be installed via PEAR (version 3.7.4 is the minimum required).
[Click here to visit the Guzzle PEAR installation instructions.](http://guzzlephp.org/getting-started/installation.html#pear)

Once you download the library, move the stormpath-sdk-php folder to your project directory and then include the library file:

    require '/path/to/stormpath-sdk-php/src/Stormpath/Stormpath.php';

You're ready to connect with Stormpath!

## Run the tests

In order to run the tests you need to clone the repository, install the dependencies via composer and configure the api key file location. These
tests require that your computer is able to communicate with the Stormpath REST API, as they perform requests to the Stormpath servers.

To perform the installation:

    git clone https://github.com/stormpath/stormpath-sdk-php.git
    cd stormpath-sdk-php && curl -s http://getcomposer.org/installer | php && ./composer.phar install --dev

Now configure the api key file location and run the tests:

    export STORMPATH_API_KEY_FILE_LOCATION=/path/to/file/apiKey.properties
    vendor/bin/phpunit

## Documentation

For additional information, please see the full [Project Documentation](http://docs.stormpath.com/php/product-guide).
