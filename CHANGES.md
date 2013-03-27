stormpath-sdk-php Changelog
===========================

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