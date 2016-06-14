stormpath-sdk-php Changelog
===========================


Version 1.15.0
--------------

Released on June 14, 2016 

 - PSR-6 Caching (Resolves #130, Fixes #24, Fixes #48)
 - SAML Tests grouped for easy developer plan testing (Fixes #131)
 - Update Readme for moved docs to http://docs.stormpath.com/php/product-guide/latest/
 
 
Version 1.14.0
--------------

Released on March 16, 2016 

 - Documentation updates
 - Using openssl for apiKey options (Fixes #132)
 - Added Github Templates for PRs and Issues (Fixes #138)
 - Passing code to Exception off of Resource Exception (Fixes #138)
 - Update Facebok Provider to include code and access_token (Fixes #142)
 - Official Support for PHP 7 (Fixes #144)
 - Updated PHP Sec Library (Fixes #144)
 - Added .gitattributes for cleaner composer (Fixes #123)
 
Version 1.13.0
--------------

Released on January 26, 2016

 - Added SAML to the SDK!
 - Add ability to use the password reset token from the normal workflow inside of the ID Site
 - Updated Copyright Dates in all files.
 - Updated travis notification to send message to HipChat channel
 

Version 1.12.2
--------------

Released on December 16, 2015

 - Fixed a bug where the accessToken and refreshTokens were not accessible from the Account
 - We now show the refresh token object from OauthGrantAuthenticatorResult for all requests if enabled
 - Removed logging from the phpunit tests. If code coverage is needed, you should now use the flag on phpunit
 - Update Readme Documentation to reference the fixes and phpunit changes
 - Removed branch alias from composer.json file

Version 1.12.1
--------------

Released on December 15, 2015

 - Social Providers isNewAccount was always returning null.  This has been fixed (Fixes Issue #109)
 - Handle ID Site would throw new exception if account was not valid, This prevented you from accessing state or any other properties
 You can not access all properties, and account will just remain null. (Fixes Issue #110)
 - Documentation issue on API Keys and Account for Request Authenticators. (Fixes Issue #105)
 - Update the Base Test so it is not treated as a test. 

Version 1.12.0
--------------

Released on November 5, 2015

- GA Release of the SDK ... YAY!
- Addition of Organization Resource. Fixes Issue #95
- Ability to specify nameKey during ID Site. Fixes Issue #93
- Give you ability to return sptoken object from Password Reset Email. Fixes Issue #102
- Reset Password during Password Reset Workflow. Fixes Issue #103
- Makes the SDK PSR-4 compliant. Fixes Issue #17


Version 1.11.0.beta
------------------

Released on October 29, 2015

- Add Integration flag to the Client. Set it from Integration with static call.
- Token Management with the application. Handles Issue #59
- Token Authentication. Lets Stormpath generate access_token and refresh_token for you from the oauth/token endpint. Handles Issue #59
- Exchange ID Site JWT with an Access Token. Handles Issue #94
- Add 2 new providers, Linkedin and Github. Handles Issue #90
- Documentation change for building a Client. Handles Issue #78

Version 1.10.0.beta
------------------

Released on September 1, 2015

- Ability to change Authentication Scheme.  Fixes Issue #12
- Better error handling in ID Site callback.  Handles Issue #82


Version 1.9.0.beta
------------------

Released on August 10, 2015

- Password Import is now tested for and officially supported 
- New method added for getting size of collection.
- Tests implemented for getting a resource based on an HTML fragment
- Resending an email verification to a user
- Added support for modifying registration workflows
- Removed PHP 5.3 Support
- Updated code coverage and updated PHPUnit package



Version 1.8.1.beta
------------------

Released on August 6, 2015

- Fixed bug with verifyEmailToken method.  This will now allow the return of an Account after verification


Version 1.8.0.beta
------------------

Released on July 30, 2015

- Added integration for Coveralls.io
- Implemented API Key generation for accounts
- Implemented API Key authorization


Version 1.7.0.beta
------------------

Released on July 29, 2015

- Upgraded to PHP-JWT to 2.2.*
- Changed to guzzle/guzzle from guzzle/html


Version 1.6.0.beta
------------------

Released on July 14, 2015

- Fixed a bug when adding custom data to an existing property (like adding an entry to an existing array).
- Added minor documentation changes.


Version 1.5.0.beta
------------------

Released on June 12, 2015

- Implemented social integration with Google and Facebook.
- Fixed a bug when saving custom data after retrieving it from a resource.


Version 1.4.0.beta
------------------

Released on June 3, 2015

- Added the capability to support the specification of an account store for password reset.
- Added tests for resource creation with custom data.
- Changed the way that the user agent is resolved to gather more information.


Version 1.3.0.beta
------------------

Released on May 7, 2015

- Added Id Site support.
- Added the capability to support the specification of an account store for account authentication.

Version 1.2.1.beta
------------------

Released on April 22, 2015

- Upgraded dependency version of the guzzle/http library to 3.9.*.

Version 1.2.0.beta
------------------

Released on April 13, 2015

- Added custom data support for Account, Application, Directory, Group and Tenant resources.

Version 1.1.0.beta
------------------

Released on February 23, 2015

- Added cache support, with built in support for Array, Redis and Memcache cache stores.

Version 1.0.1.beta
------------------

Released on March 31, 2014

- Fixed create account and create group tests.
- Fixed warning about variables not initialized in BaseTest class.

Version 1.0.0.beta
------------------

Released on October 24, 2013

- Major overhaul of SDK that includes using namespaces, make it psr-0 compliant, changing the HTTP library (using Guzzle) and API changes.
- New test suite that runs from an apiKey.properties file.
- Added support for pagination, search, expansion and ordering.
- Implemented application centric operations.
- Implemented account store mappings functionalities.

Version 0.3.2
-------------

Released on June 27, 2013

- Added http redirection support to Services_Stormpath_Http_HttpClientRequestExecutor to fix current tenant retrieval issue.

Version 0.3.1
-------------

Released on March 27, 2013

- Fixing bug where the createAccount() method of the Services_Stormpath_Resource_Directory class was not returning the created account.

Version 0.3.0
-------------

Released on December 21, 2012

- The Services_Stormpath_Resource_GroupMembership class now extends the Services_Stormpath_Resource_Resource class. It is no longer possible to call save() on an instance of this class.
- The create() method of the Services_Stormpath_Resource_GroupMembership class is now static and receives an instance of Services_Stormpath_DataStore_InternalDataStore; it was renamed to _create().
- The addGroup() method implementation of the Services_Stormpath_Resource_Account class was updated to reflect the previously mentioned changes.
- The addAccount() method implementation of the Services_Stormpath_Resource_Group class was updated to reflect the previously mentioned changes.

Version 0.2.0
-------------

Released on August 31, 2012

- The getProperties() method is no longer available for the resources. The properties are now obtained via Services_Stormpath_Resource_Resource's getPropertyNames() and getProperty() functions.
- A simple __toString() implementation was added on the Services_Stormpath_Resource_Resource class.
- Logic to retain non-persisted properties was added (dirty properties).
- A resource's property can now be removed by setting it to null.
- The Services_Stormpath_Client_ClientApplicationBuilder class was added and implemented to produce a Services_Stormpath_Client_ClientApplication from a single URL with the credentials on it.

Version 0.1.0
-------------

Released on August 22, 2012

- First release of the Stormpath PHP SDK where all of the features available on the REST API by the release date were implemented.