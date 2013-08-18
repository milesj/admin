# Admin v1.1.1 #

A CakePHP administration plugin that provides moderate CRUD functionality for application and plugin models.
Utilizes CakePHP's built-in authentication, authorization and ACL systems for security.

This plugin *does not work* with the CakeDC Users plugin or with any tables that uses UUIDs.
It also requires admin Routing prefixes to be *disabled*.

## Requirements ##

* PHP 5.3.*
	* Multibyte
* CakePHP 2.3.*
	* ACL
	* Auth
	* Utility Plugin v1.5.* - https://github.com/milesj/Utility
* Composer

## Quick Installation ##

Install the plugin with Composer.

```
composer update
```

Bootstrap the plugins and its routes.

```php
CakePlugin::loadAll();
CakePlugin::load('Utility', array('bootstrap' => true, 'routes' => true));
CakePlugin::load('Admin', array('bootstrap' => true, 'routes' => true));
```

Enable ACL in core.

```php
Configure::write('Acl.classname', 'DbAcl');
Configure::write('Acl.database', 'default');
```

Create the ACL tables if you have not already.

```
cake schema create DbAcl
```

Set the ARO administrator alias in your bootstrap (defaults to Administrator).

```php
Configure::write('Admin.aliases.administrator', 'Administrator');
```

Install the plugin through the command line (this will generate appropriate ACL records).

```
cake Admin.install
```

Install the plugins and models to enable CRUD functionality and access permissions.

```
cake Admin.install plugin Forum
cake Admin.install model User
```