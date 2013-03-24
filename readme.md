# Admin v0.13.0 #

A CakePHP administration plugin that provides moderate CRUD functionality for application and plugin models.
Utilizes CakePHP's built-in authentication, authorization and ACL systems for security.

This plugin *does not work* with the CakeDC Users plugin or with any users table that uses UUIDs.
It also requires admin Routing prefixes to be *disabled*.

## Requirements ##

* PHP 5.3.0
	* Multibyte
* CakePHP 2.3.0
	* ACL
	* Auth
	* Utility Plugin v1.4.0 - https://github.com/milesj/Utility
* Composer

## Quick Installation ##

Install the plugin with Composer.

```
composer update
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

Set the ARO administrator alias in your bootstrap (defaults to administrator).

```php
Configure::write('Admin.aliases.administrator', 'administrator');
```

Install the plugin and follow the on screen instructions (this will generate appropriate ACL records).

```
cake Admin.install
```

Install the plugins and models to enable CRUD functionality and enable access permissions.

```
cake Admin.install plugin Forum
cake Admin.install model User
```

Bootstrap the plugin and its routes.

```php
CakePlugin::load('Admin', array('bootstrap' => true, 'routes' => true));
```