<div class="action-buttons">
    <?php
    $links = array(
        'index' => array(
            'title' => __d('admin', 'Plugins'),
            'icon' => 'fa fa-paste'
        ),
        'models' => array(
            'title' => __d('admin', 'Models'),
            'icon' => 'fa fa-file'
        ),
        'config' => array(
            'title' => __d('admin', 'Configuration'),
            'icon' => 'fa fa-cog'
        ),
        'cache' => array(
            'title' => __d('admin', 'Cache'),
            'icon' => 'fa fa-hdd-o'
        ),
        'routes' => array(
            'title' => __d('admin', 'Routes'),
            'icon' => 'fa fa-road'
        ),
        'logs' => array(
            'title' => __d('admin', 'Logs'),
            'icon' => 'fa fa-exchange',
            'url' => array('controller' => 'logs', 'action' => 'read', 'error')
        ),
        /*'locales' => array(
            'title' => __d('admin', 'Locales'),
            'icon' => 'fa fa-globe'
        )*/
    );

    foreach ($links as $action => $link) {
        $class = 'button';
        $url = array('controller' => 'admin', 'action' => $action);

        if ($this->action === $action) {
            $class .= ' is-active';
        }

        if (!empty($link['url'])) {
            $url = $link['url'];
        }

        echo $this->Html->link('<span class="' . $link['icon'] . '"></span> ' . $link['title'],
            $url,
            array('class' => $class, 'escape' => false));
    } ?>

    <span class="clear"></span>
</div>