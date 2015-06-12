stormpath-sdk-php Changelog
===========================


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