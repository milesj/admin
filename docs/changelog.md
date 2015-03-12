# Changelog #

*These logs may be outdated or incomplete.*

## 1.3.3 ##

* Includes changes from previous versions
* Added missing biginteger column type
* Added a `hideReadFields` option to models
* Fixed a bug where `Admin.coreName` was not used consistently
* Fixed a bug where avatar size did not scale correctly
* Menu items and links will now show or hide depending on the content
* Index action links are now customizable

## 1.3.0 ##

* Updated Titon Toolkit to 1.3.0
* Added `hideColumns` option to models [[#16](https://github.com/milesj/admin/issues/16)]
* Fixed a bug where empty checkbox wasn't auto-checked for textarea fields [[#17](https://github.com/milesj/admin/issues/17)]

## 1.2.0 ##

* Updated to Titon Toolkit 1.0
* Updated to FontAwesome 4.0
* Changed MooTools to jQuery
* Added a create button to has many record table
* Added a model link dropdown for records index
* Added missing data types to read views: binary, datetime, float

## 1.1.3 ##

* Updated Titon to the latest front-end

## 1.1.2 ##

* Includes changes from 1.1.1
* Updated Titon Toolkit to v0.6.0
* Removed old schema files

## 1.1.0 ##

* Added ACL and roles detection into the session layer (allows for easy use between plugins)
* Added `ClassRegistry::init()` in `Admin::introspectModel()` as it resolves includes successfully
* Added a badge counter system to `Admin.menu` items
* Replaced Twitter Bootstrap with [Titon Toolkit](https://github.com/titon/Toolkit)
* Improved usability and user experience

## 1.0.3 ##

* Fixed `RequestObject` throwing exceptions while checking user node path
* Added helper methods to `RequestObject` to improve role calculation

## 1.0.2 ##

* Fixed a bug where a required attribute on inputs were breaking filter forms
* Fixed a bug where type ahead inputs were losing value once filtering
* Refactored the search icon display for belongs to input fields

## 1.0.1 ##

* Updated `AdminAppController` to extend from `AppController` [[#5](https://github.com/milesj/Admin/issues/5)]

## 1.0.0 ##

* Initial release of the Admin plugin
