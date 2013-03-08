# Admin #

In development.

## Quick Installation ##

Install the plugin with Composer.

```
composer update
```

Create the ACL tables if you have not already.

```
cake schema create DbAcl
```

Set the ARO administrator alias in your bootstrap (defaults to administrator).

```php
Configure::write('Admin.adminAlias', 'administrator');
```

Install the plugin and follow the on screen instructions (this will generate appropriate ACL records).

```
cake Admin.install
```

Install the plugins and models to enable CRUD functionality and enable access permissions.
The Core plugin refers to the application models; this allows for quick installation of all models.

```
cake Admin.install plugin Core
cake Admin.install plugin Forum
```

Or install individual models.

```
cake Admin.install model User
```