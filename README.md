# Stormpath PHP SDK 

[![Build Status](https://api.travis-ci.org/stormpath/stormpath-sdk-php.svg?branch=master,dev)](https://travis-ci.org/stormpath/stormpath-sdk-php) 
[![Codecov](https://img.shields.io/codecov/c/github/stormpath/stormpath-sdk-php.svg)](https://codecov.io/github/stormpath/stormpath-sdk-php)
[![Total Downloads](https://poser.pugx.org/stormpath/sdk/d/total.svg)](https://packagist.org/packages/stormpath/sdk)
[![Latest Stable Version](https://poser.pugx.org/stormpath/sdk/v/stable.svg)](https://packagist.org/packages/stormpath/sdk)
[![License](https://poser.pugx.org/stormpath/sdk/license.svg)](https://packagist.org/packages/stormpath/sdk)
[![Chat](https://img.shields.io/badge/chat-on%20freenode%20-green.svg)](http://webchat.freenode.net/?channels=#stormpath)
[![Support](https://img.shields.io/badge/support-support@stormpath.com-blue.svg)](mailto:support@stormpath.com?subject=Stormpath+PHP+SDK)

[Stormpath](https://stormpath.com) is a complete user management API.  This
library gives your PHP application access to all of Stormpath's features:

- Robust authentication and authorization.
- Schemaless user data and profiles.
- A hosted login subdomain, for easy Single Sign-On across your apps.
- External login with social providers like Facebook and Google, or SAML IdPs.
- Secure API key authentication for your service.


## Installation

**stormpath-sdk-php** is available on Packagist as the [stormpath/sdk](http://packagist.org/packages/stormpath/sdk) package.

Run `composer require stormpath/sdk` from the root of your project in terminal, and you are done.


## Quickstart

To learn how to use the Stompath PHP SDK in a simple project, follow our quickstart:

* [PHP Quickstart](http://docs.stormpath.com/php/product-guide/latest/quickstart.html)

#### Full Documentation

We have moved our full documentation away from the Github readme file.  For full documentation, please visit [our new documentation](http://docs.stormpath.com/php/product-guide/latest)


## Testing

The PHP SDK uses `phpunit` for testing.  These tests are full integration tests which means it hits actual endpoints of the API.

To setup testing, first, clone the repository.  You will not be able to run the tests from your vendor folder as all unnecessary items are removed
when you require the SDK with composer.  You will also need to set an environment variable to set up your API keys. 

#### On Mac
    
    export STORMPATH_SDK_TEST_API_KEY_FILE_LOCATION=path_to_apiKey.properties_file

#### On Windows
  
    setx STORMPATH_SDK_TEST_API_KEY_FILE_LOCATION path_to_apiKey.properties_file
    
After you have this set, you will be able to run `phpunit` from the root of the SDK folder.  If your API keys are not from a subscription that has access to 
the SAML resources, you will have to make sure you skip those tests.  You can do so by running `phpunit --exclude-group=saml` from the command line.

## Contributing

Contributions, bug reports, and issues are very welcome! Stormpath regularly maintains this repository, and are quick to review pull requests and accept changes.

You can make your own contributions by forking the develop branch of this repository, making your changes, and issuing pull requests against the `develop` branch.

#### Continuous Integration (Travis CI)
Please note that due to security reasons, travis will not run pull requests submitted.  With your pull request, please submit the results of the tests in the comments.

#### Documentation
If you feel the contributions require document changes as well, or the contributions you want to make are for documentation, please submit a PR to [our documentation repo](https://github.com/stormpath/stormpath-documentation)


## Copyright

Copyright &copy; 2013-2017 Stormpath, Inc. and contributors.

This project is open-source via the [Apache 2.0 License](http://www.apache.org/licenses/LICENSE-2.0).