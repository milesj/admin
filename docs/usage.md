# Admin #

*Documentation may be outdated or incomplete as some URLs may no longer exist.*

*Warning! This codebase is deprecated and will no longer receive support; excluding critical issues.*

The Admin plugin provides an easy to use interface for managing all aspects of an application. Supports CRUD functionality for all models. Utilizes ACL and Auth for permissions and access rights. Built-in activity logging, item reporting, and much more.

### Requirements: ###

* [Utility Plugin](https://github.com/milesj/Utility)

## Preparation ##

The plugin requires [Composer](http://getcomposer.org/) for installation so that all dependencies are also installed. [Learn more about using Composer in CakePHP](http://milesj.me/blog/read/using-composer-in-cakephp). The plugin requires the [Utility plugin](http://milesj.me/code/cakephp/admin) as a dependency.

```javascript
{
    "config": {
        "vendor-dir": "Vendor"
    },
    "require": {
        "mjohnson/admin": "1.*"
    }
}
```

Be sure to enable Composer at the top of `Config/core.php`.

```php
require_once dirname(__DIR__) . '/Vendor/autoload.php';
```

And to load the plugins (bootstrap and routes required).

```php
// Utility should be loaded first
CakePlugin::load('Utility', array('bootstrap' => true, 'routes' => true));
CakePlugin::load('Admin', array('bootstrap' => true, 'routes' => true));
```

 [Learn more about loading plugins correctly!](http://milesj.me/blog/read/plugin-loading-quirks)

## Installation ##

The plugin utilizes CakePHP's built-in ACL system. [Install the ACL database tables](http://book.cakephp.org/2.0/en/console-and-shells/acl-shell.html) and configure the ACL settings before installing the plugin.

```php
Configure::write('Acl.classname', 'DbAcl');
Configure::write('Acl.database', 'default');
```

The default ARO (access request object) alias for the administrator ACL role is "Administrator". To change this value, update the configuration, else omit the change.

```php
Configure::write('Admin.aliases.administrator', 'Administrator');
```

Install the plugin by executing the following shell on the command line (this will generate appropriate ACL records).

```bash
# From within the app/ folder
Console/cake Admin.install
```

Since the plugin integrates into an external users system, the admin panel will *not* provide a login or logout interface. This type of functionality is left up to the developer to implement outside of the plugin in the application. The admin requires the Auth and ACL components to work correctly.

## Installing Models ##

To enable CRUD functionality for models, the associated ACL permissions must be created. Any models that have not been installed will have a red exclamation box next to their name on the dashboard index. To install (e.g., create) ACL records, run the following commands from the command line.

```bash
Console/cake Admin.install model ModelName
```

Or install a model from within a plugin.

```bash
Console/cake Admin.install model Plugin.ModelName
```

Or install all models from within a plugin.

```bash
Console/cake Admin.install plugin Plugin
```

## CRUD Operations ##

The basic concept behind the admin plugin is that all models are integrated using CRUD, which stands for create, read, update, delete. The plugin does exactly that, provides a simple interface for creating, reading, updating and deleting of records for an application or plugin model. On top of that, every action is restricted through ACL requiring specific access permissions to modify records.

### Index ###

The index of every model will list out and paginate records. The records will fetch and display `$belongsTo` data by utilizing the models `$displayField` and `$primaryKey`. If models inherit `Utility.Enumerable`, their values will seamlessly be replaced by their enumerable equivalent (the same applies to forms).

A list of filters are built by inspecting the current model and generating the appropriate input fields. These filters can be applied to reduce the current result set. For further management, behavior callbacks can be triggered through a dropdown menu, and batch processing of model callbacks can be executed through a form.

### Create, Update ###

The create and update actions work in a similar fashion. Every form has functionality derived from the models validation rules, database schema and associations. This allows for input fields to set requirements, null checks and more. Any `$belongsTo` field is represented as a drop down list or a type ahead field (depending on how many records exist in the database). Minor support for `$hasAndBelongsToMany` relations is used accordingly.

### Read ###

The read action provides an overview of a record including full associated data. Be careful as this action will pull in all data unless limits and conditions are placed on associations.

### Delete ###

Lastly, the delete action, which provides a confirmation of deletion (because everyone hates accidental deletions). The confirmation will display a nested list of dependent associated records that will also be deleted.

## Configuration ##

Like any application, the plugin is packaged with many configurable settings. The basic settings will be covered in this chapter, while the more advanced will be covered in later chapters. The default settings can be found in `Plugin/Admin/Config/bootstrap.php`.

### appName ###

This will change the title at the top left of the admin interface. Defaults to "Admin".

```php
Configure::write('Admin.appName', 'Admin');
```

### coreName ###

The plugin provides an extremely robust CRUD mapper for application and plugin models. To increase reliability and interoperability of plugin vs non-plugin models, all non-plugin models were prefixed with a pseudo plugin named "Core". All non-plugin models should be written as "Core.ModelName" when applicable. This value can be changed by overwriting coreName, but be sure not to conflict with actual plugins!

```php
Configure::write('Admin.coreName', 'App');
```

### aliases ###

This setting provides a mapping of ARO top-level roles like administrator and super moderator. It allows for custom role names to be used that do not match the plugin defaults, or are used externally. The default roles are Administrator and SuperModerator.

```php
Configure::write('Admin.aliases.administrator', 'Administrator');
// or Admin.aliases.superModerator
```

### ignoreModels ###

An array of models that should be hidden from within the admin. Application models should be prefixed with the coreName value.

```php
Configure::write('Admin.ignoreModels', array('Core.User', 'Plugin.Model'));
```

### logActions ###

Enable or disable internal activity logging. Logging is enabled by default.

```php
Configure::write('Admin.logActions', false);
```

## User Customizing ##

The admin plugin integrates into an existing users system and application. Since the plugin is unaware of the users table structure, it assumes specific values to be used, and sometimes these need to be changed. These same settings are used in other plugins like the [Forum Plugin](http://milesj.me/code/cakephp/forum).

### model ###

To change the model to something besides User, define the `USER_MODEL` constant before the plugin is loaded. The constant also supports plugin syntax.

```php
define('USER_MODEL', 'Member');
```

### fieldMap ###

Like the name suggests, this setting provides a mapping of database fields. The following fields are mapped: username, password, email, status, avatar, locale, timezone, lastLogin. The default values match the name of the field, to change them, update `User.fieldMap`.

```php
// Single value
Configure::write('User.fieldMap.username', 'displayName');

// Multiple values
Configure::write('User.fieldMap', array(
    'username' => 'displayName',
    'email' => 'emailAddress'
) + Configure::read('User.fieldMap'));
```

### statusMap ###

Similar to the `fieldMap`, the `statusMap` is a mapping of a users current status (or active state). The following statuses are mapped: pending (0), active (1), and banned (2).

```php
Configure::write('User.statusMap', array(
    'pending' => 'pending',
    'active' => 'active',
    'banned' => 'banned'
) + Configure::read('User.statusMap'));
```

### routes ###

This setting provides a mapping of common user related routes. The following routes are mapped: login, logout, signup, forgotPass, settings, profile. Each route is mapped to the users controller within the main application. 

The profile route supports special tokens that will be replaced with dynamic user data. These tokens are {id}, {slug}, and {username}.

```php
Configure::write('User.routes.profile', array('plugin' => false, 'controller' => 'user', 'action' => 'index', 'slug' => '{slug}'));
```

## Model Settings ##

To apply administration settings for every model, a property named `$admin` must be defined. If this property does not exist, or is false, the model will be hidden in the admin. In turn, the property can be enabled with a true value, or an array of settings. The following settings are available:

* `imageFields` (array) - Fields that store image URL paths (defaults to image)
* `fileFields` (array) - Fields that store file paths; Also turns input fields into file fields (defaults to image, file)
* `hideFields` (array) - Fields that will be hidden during create and update forms (defaults to lft, rght)
* `editorFields` (array) - Fields (usually text) that will use an advanced input editor
* `editorElement` (string) - Name of element that will be included to trigger WYSIWYG functionality on the editor field
* `paginate` (array) - Pagination settings for the index table list
* `associationLimit` (int:75) - Number of associated records to display in dropdown menus before switching to type ahead menus
* `batchProcess` (bool:true) - Allows batch processing of model records
* `actionButtons` (bool:true) - Display read, update, delete buttons within tables
* `deleteable` (bool:true) - Enable or disable delete actions (overrides ACL delete)
* `editable` (bool:true) - Enable or disable update actions (overrides ACL update)
* `iconClass` (string) - Font awesome icon class name

Settings are defined on a per model basis, but will inherit the default settings. The default model settings can be found in `Admin.modelDefaults` and can be updated through Configure. Be careful as these settings apply to all models, and not individual models.

```php
Configure::write('Admin.modelDefaults', array(
    'imageFields' => array('image')
) + Configure::read('Admin.modelDefaults'));
```

A few implementation examples:

```php
// Disable in admin
class Post extends AppModel {
    public $admin = false;
}

// Enable in admin using default settings
class User extends AppModel {
    public $admin = true;
}

// Enable with custom settings
class Image extends AppModel {
    public $admin = array(
        'imageFields' => array('imageLarge', 'imageMedium', 'imageSmall'),
        'fileFields' => array('path'),
        'iconClass' => 'icon-image'
    );
}
```

Settings simply prepare the model for interaction in the admin, but does not enable it completely. A model will require ACL permissions before CRUD functionality is usable. Jump to the "Installing Models" chapter to learn about installing models.

### Using an editor ###

To apply a WYSIWYG style editor to an input field, add the field to the `editorFields` setting. Create an element in the application and set the element name in `editorElement`. This element will then bootstrap and initialize the editor on `$inputId` (a variable containing the HTML ID of the input). Here's a quick example element that loads the [MarkItUp](http://markitup.jaysalvat.com) library.

```php
// Elements/admin_editor.ctp
$this->Html->script('jquery.markitup', array('inline' => false));
$this->Html->script('markitup/default', array('inline' => false));
$this->Html->css('markitup', 'stylesheet', array('inline' => false));
$this->Html->css('markitup/default', 'stylesheet', array('inline' => false));
```
```javascript
<script type="text/javascript">
    $(function() {
        $('#<?php echo $inputId; ?>').markItUp(mySettings);
    });
</script>
```

## Model & Behavior Callbacks ##

Callbacks are a system that permit model and behavior methods to be executed from within the admin interface, for example, banning a user. Behavior callbacks are triggered on the index listing of a model. Model callbacks are triggered on the read page and report management page. The callbacks can be triggered by clicking on the "Process" action button and selecting the callback from the dropdown list.

To define new callbacks, update the `Admin.modelCallbacks` and `Admin.behaviorCallbacks` configurations. Each entry should contain a mapping of class methods to display titles. The %s token within the title string will be substituted with the current inflected model name.

 Application models must be prefixed with "Core." or the value of Admin.coreName.

```php
// Models
Configure::write('Admin.modelCallbacks', array(
    'Core.User' => array(
        'ban' => 'Ban %s',
        'activate' => 'Activate %s',
        'deactivate' => 'Deactivate %s'
    )
) + Configure::read('Admin.modelCallbacks'));

// Behaviors
Configure::write('Admin.behaviorCallbacks', array(
    'Cacheable' => array(
        'clearCache' => 'Clear Cache'
    )
) + Configure::read('Admin.behaviorCallbacks'));
```

The model callback method will receive the record ID as a single argument.

```php
class User extends AppModel {
    public function ban($id) {
        // Process
    }
}
```

The behavior callback method will receive the current model instance as a single argument. These methods should be used for general all purpose functionality.

```php
class CacheableBehavior extends ModelBehavior {
    public function clearCache(Model $model) {
        // Clear cache
    }
}
```

The CacheableBehavior is part of the [Utility Plugin](https://github.com/milesj/Utility) and is enabled by default within the admin. The previous was merely an example.

## Action Overrides ##

The admin CRUD will cover most cases when model attributes, associations, and validation rules are defined properly. However, there are off cases where the basic CRUD is not powerful enough, so the action overrides system exists. This system allows external controller actions to override the default admin CRUD actions.

### Configuration ###

To apply overrides, append an array of model CRUD mappings to `Admin.actionOverrides`. The array index should be the fully qualified model name and the value should be an array of CRUD to controller overrides. The following example overrides the update action for the `User` model and uses the applications `UsersController::admin_update()`. It also overrides a plugin.

```php
Configure::write('Admin.actionOverrides', Configure::read('Admin.actionOverrides') + array(
    'Core.User' => array(
        'update' => array('plugin' => false, 'controller' => 'users', 'action' => 'admin_update')
    ),
    'Plugin.Model' => array(
        'create' => array('plugin' => false, 'controller' => 'override', 'action' => 'admin_create'),
        'update' => array('plugin' => false, 'controller' => 'override', 'action' => 'admin_update')
    )
));
```

### Implementation ###

Up next is creating the controller action &mdash; which must follow a few conventions. All overridden actions should be prefixed with `admin_`, the primary model should be set to `$this->Model`, and the controller should include the `AdminToolbarComponent` (highly required!). Here's a full implementation for the `User` override.

```php
public function admin_update($id) {
    $this->Model = Admin::introspectModel('Core.User');
    $this->Model->id = $id;

    $result = $this->AdminToolbar->getRecordById($this->Model, $id);
    $singularName = $this->Model->singularName;

    if (!$result) {
        throw new NotFoundException(__d('admin', '%s Not Found', $singularName));
    }

    if ($this->request->is('post')) {
        if ($this->Model->saveAll($this->request->data, array('validate' => 'first', 'atomic' => true, 'deep' => true))) {
            $this->AdminToolbar->logAction(ActionLog::UPDATE, $this->Model, $id, 'Updated user', $this->request->data[$this->Model->alias][$this->Model->displayField]);

            $this->AdminToolbar->setFlashMessage(__d('admin', 'Successfully updated %s with ID %s', array(mb_strtolower($singularName), $id)));
            $this->AdminToolbar->redirectAfter($this->Model);
        } else {
            $this->AdminToolbar->setFlashMessage(__d('admin', 'Failed to update %s with ID %s', array(mb_strtolower($singularName), $id)), 'error');
        }
    }

    $this->set('result', $result);
}
```

And the view to accompany it. The admin elements and the `AdminHelper` should be used so that the view matches the admin panel. The model being modified can be found at `$model`.

```php
<?php $this->Admin->setBreadcrumbs($model, $result, $this->action);

echo $this->element('Admin.crud/actions'); ?>

<h2><?php echo $this->Admin->outputIconTitle($model, __d('admin', 'Edit %s', $model->singularName)); ?></h2>

<?php
echo $this->Form->create($model->alias, array('class' => 'form-horizontal', 'type' => 'file'));

// Input fields for every database column
echo $this->element('Admin.crud/form_fields');

// Input fields for associations
echo $this->element('Admin.crud/form_extra');

// Input field for a single column
echo $this->element('Admin.input', array(
    'field' => 'dbColumn',
    'data' => array(
        'type' => 'string', // string, text, biginteger, integer, float, datetime, timestamp, time, date, binary, boolean
        'title' => 'Database Column',
        'null' => false // Display empty checkbox
    )
));

// Action buttons
echo $this->element('Admin.form_actions');
echo $this->Form->end();
```

 The Forum plugin makes use of overrides. [View the configuration](https://github.com/milesj/Forum/blob/master/Config/bootstrap.php#L99) or [the controller action](https://github.com/milesj/Forum/blob/master/Controller/StationsController.php#L192) or the [view implementation](https://github.com/milesj/Forum/blob/master/View/Stations/admin_delete.ctp).

## View Overrides ##

Unlike action overrides which replace the controller action and the view, the view overrides only replace the view. This permits the default `CrudController` functionality to be used while rendering a custom view.

### Configuration ###

View overrides are configured exactly like action overrides. The only difference is that the route directs to the view, instead of the controller action. The following example overrides the index action for the `Post` model.

```php
Configure::write('Admin.viewOverrides', Configure::read('Admin.viewOverrides') + array(
    'Core.Post' => array(
        'index' => array('plugin' => false, 'controller' => 'posts', 'action' => 'admin_index')
    )
));
```

### Implementation ###

Like the previous chapter, Action Overrides, the view should make use of the `AdminHelper` and the admin elements. I will not be providing an example, as it's best to check out the elements in the admin plugin, as well as understanding how the current CRUD views are written. However, here are some variables that are bound to the view.

* `$model` - The current introspected model
* `$result` (read, update, delete) - The current database record
* `$results` (index) - A list of database records
* `$pendingReports` - Count of how many unresolved reports
* `$typeAhead` (index, create, update) - Mapping of associated data used in type aheads
* `$counts` (read) - Count of how many records exist for each association

## ACL Permissions ##

The plugin makes use of CakePHP's ACL system which is divided into 3 parts: AROs, ACOs and permissions. AROs, also known as Request Objects, and ACOs, also known as Control Objects, both provide hierarchial inheritance and an alias field to distinguish records.

* **Request Objects** (ARO) - Objects requesting access
* **Control Objects** (ACO) - Objects being controlled / being requested against
* **Object Permissions** - Provides CRUD access between requesters and controllers

A matrix table depicting the 3 parts can be found under the ACL menu tab. Each row represents a controller, each column represents a requester, and every intersecting cell represents the permission between the two. Cells are colored to distinguish their purpose. Green cells provide full access, while blue cells inherit from the parent, while red cells restrict access, and yellow cells have no access defined. Clicking on a cell will either provide access, or allow the current permission to be changed.

### Controllers ###

Controllers are objects that are requested or interacted with by requesters. They will require different levels of access for different requesters. If a requester does not have access, an unauthorized exception is thrown.

To create a new controlled object, create a new control object that leaves the parent (optional), model and foreign key fields empty.

### Roles ###

Roles (also called groups) are merely top-level requesters. The admin plugin provides an administrator role under `Admin.aliases.administrator` (alias defaults to Administrator) and a super moderator role under `Admin.aliases.superModerator` (alias defaults to SuperModerator). Roles should provide permissions against all controllers, so that access is inherited through children (the users).

To add a new role, create a new request object that leaves the parent, model and foreign key fields empty.

### Users ###

Users are requesters that are assigned to a role (or parent requester). Users will inherit all permissions from their parent unless a user-level permission is created.

To grant access to a user, create a new request object, assign a parent role, set the model (usually `User`), the foreign key for the user in question, and an alias defining the record (usually the user's name).

## Activity Logs ##

The plugin comes bundled with a database layer logging system that will log all administrator actions and allow for manual implementation, which provides broad monitoring of users. Most logs are tied to a specific item (a single database record) and are categorized into one of the following: create, read, update, delete, process, batch delete, batch process, and other. Logs can be found under the top level "Logs" menu.

Every database record that is modified through the admin will require a comment on why this change is being made. This allows for a deep history of changes and notes from administrators. One can modify the `Admin.logActions` setting to disable automatic admin panel logging.

```php
Configure::write('Admin.logActions', false);
```

By default, a unique action can only be logged once every 6 hours. To alter the time interval, change `Admin.logActions.interval` (use a negative timeframe).

```php
Configure::write('Admin.logActions.interval', '-12 hours');
```

### Manual Logging ###

Besides the automatic admin logging, the logger can be used manually to track actions within the primary application. Start by adding the `AdminToolbarComponent` to the controller that wishes to track.

```php
public $components = array('Admin.AdminToolbar');
```

Then call the `logAction($action, $model, $id, $comment, $item)` method to insert a record into the database. The only required argument is the 1st, which should be one of the `ActionLog` constants. More information on the arguments can be found below.

* `$action` (int) - One of the `ActionLog` constants: `CREATE`, `READ`, `UPDATE`, `DELETE`, `BATCH_DELETE`, `PROCESS`, `BATCH_PROCESS`, `OTHER`
* `$model` (Model) - A `Model` instance pertaining to a specific item, or empty for no model
* `$id` (int) - ID of the record being logged, or empty for no record
* `$comment` (string) - Comment pertaining to the record or log
* `$item` (string) - Title or name of the model record being logged (like the users username)

```php
// Log a single model record
$this->AdminToolbar->logAction(ActionLog::CREATE, $this->User, $this->User->id, 'Created user', 'miles');

// Log multiple model records
$this->AdminToolbar->logAction(ActionLog::BATCH_DELETE, $this->User, null, 'Deleted multiple users');

// Log a general action
$this->AdminToolbar->logAction(ActionLog::PROCESS, null, null, 'Something happened');
```

Logs require a logged in user via the AuthComponent to associate with.

## Item Reporting ##

The plugin also comes bundled with a reporting system that allows users to report items that can be resolved by administrators. All reports are tied to a specific item (a single database record) and are categorized into one of the following: violence, offensive, hateful, harmful, spam, copyright, sexual, harassment, and other. Reports can be found under the top level "Reports" menu.

Each report can be viewed in detail that will include the reported item's data. From this view, an administrator can process the report either using a `Admin.modelCallbacks` method, or mark the report as invalid, or delete the reported item. If logging is enabled, a comment will also be required while processing.

All unresolved reports will be represented by a numerical badge in the top admin navigation.

### Manual Reporting ###

To allow user reporting of content, a controller action would need to be created to handle the process. Start by adding the `AdminToolbarComponent` to the controller that wishes to report.

```php
public $components = array('Admin.AdminToolbar');
```

And create the action to handle it. The `reportItem($type, $model, $id, $reason, $user_id)` method will be called to create the database record. All arguments are required excluding `$reason` and `$user_id` (which is taken form auth).

```php
public function report($id) {
    $user = $this->User->findById($id);

    if (!$user) {
        throw new NotFoundException();
    }

    if ($this->request->is('post')) {
        $data = $this->request->data['Report'];

        if ($this->AdminToolbar->reportItem($data['type'], $this->User, $id, $data['reason'])) {
            $this->Session->setFlash('User has been reported');
            $this->redirect(array('action' => 'view', $id));
        }
    }

    $this->set('user', $user);
}
```

And the accompanying view.

```php
echo $this->Form->create('Report');
echo $this->Form->input('type', array('options' => $this->Utility->enum('Admin.ItemReport', 'type')));
echo $this->Form->input('reason', array('type' => 'textarea'));
echo $this->Form->end('Report');
```

More information on the `reportItem()` method is listed below.

* `$type` (int) - One of the `ItemReport` constants:
    * `VIOLENCE` - Physical fighting, General abuse
    * `OFFENSIVE` - Animal, child, etc abuse
    * `HATEFUL` - Hate crimes, Bullying
    * `HARMFUL` - Dangerous acts, Self injury
    * `SPAM` - Spam, Fraud, Misleading Ads
    * `COPYRIGHT` - Copyright or trademark infringement
    * `SEXUAL` - Nudity, Sex, Pornography
    * `HARASSMENT` - User to user, Threats, Trolling
    * `OTHER` - Doesn't fall into the previous types
* `$model` (Model) - A `Model` instance pertaining to a specific item
* `$id` (int) - ID of the record being reported
* `$reason` (string) - The users comment on why the item is being reported
* `$user_id` (int) - ID of the user reporting (defaults to `Auth::user('id')`)

## File Uploading ##

The plugin provides a simple file uploading interface that should be used to upload files (or images) that are used in news posts, or represent banner images, etc. This **should not** replace model level file uploading. File uploading will require the [Uploader plugin](http://milesj.me/code/cakephp/uploader) and can be found under the top level "Upload" tab in the menu.

If the Uploader plugin is not loaded, the upload page will display an "Install the Uploader to upload files" message with an install button. This will direct to the Uploader documentation, which should be thoroughly read to understand how the upload interface will work. If the Uploader plugin is loaded, an upload form will display with transform and transport options.

### Schema ###

For every file uploaded, the following meta data is saved into the database: `size`, `ext`, `type` (mime type), `width` (image only), `height` (image only) and `user_id` of the user who uploaded it. The path to the uploaded file (whether local or remote) will be saved into the `path` column. If an image is uploaded, 2 transformation files will be created (medium and thumbnail sizes) and the paths will be saved to the `path_thumb` and `path_large` columns.

### Configuration ###

Since this uses the Uploader plugin, both the transform and transport settings can be defined. To provide default settings, configure the `Admin.uploads.transforms` and `Admin.uploads.transport` settings. These settings will pre-populate the input fields within the upload form &mdash; fields which can be modified during the upload process.

The [transform settings](#transforming-images-resize-crop-etc) should define an array for `path_thumb` and `path_large`. Here are the default settings. There are 2 possible values to use for `nameCallback`: `formatName` which is an MD5 of the original file name, and `formatTransformName` which uses the original files name without appended or prepended strings.

```php
Configure::write('Admin.uploads.transforms', array(
    'path_thumb' => array(
        'method' => 'crop',
        'nameCallback' => 'formatTransformName',
        'append' => '-thumb',
        'overwrite' => true,
        'width' => 250,
        'height' => 150
    ),
    'path_large' => array(
        'method' => 'resize',
        'nameCallback' => 'formatTransformName',
        'append' => '-large',
        'overwrite' => true,
        'aspect' => true,
        'width' => 800,
        'height' => 600
    )
));
```

The [transport settings](#transporting-to-the-cloud) should define an array of remote storage settings. Here's a quick example for Amazon S3.

```php
Configure::write('Admin.uploads.transport', array(
    'class' => 's3',
    'accessKey' => '<access>',
    'secretKey' => '<secret>',
    'region' => 'us-east-1',
    'bucket' => '<bucket>',
    'folder' => 'uploads/'
));
```

### Usage ###

Once the Uploader is loaded and configured, files can be uploaded through the Upload page. The upload form is self-explanatory, the transport settings are on the left side, while transformation settings are on the right; these input fields will override the configuration settings. The caption field is private but can be used to provide a small comment about the file in question.

Simply select a file through the input field and upload. It's best to mess around with it to better understand its functionality.
