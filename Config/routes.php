<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

foreach (Configure::read('Admin.menu') as $section => $menu) {
    Router::connect('/admin/'. $section . '/:action/*', $menu['url'], array('section' => $section));
    Router::connect('/admin/'. $section, $menu['url'] + array('action' => 'index'), array('section' => $section));
}

Router::connect('/admin/:model/:action/*',
    array('plugin' => 'admin', 'controller' => 'crud'),
    array('model' => '[_a-z0-9]+\.[_a-z0-9]+'));

Router::connect('/admin/:action/*', array('plugin' => 'admin', 'controller' => 'admin'));