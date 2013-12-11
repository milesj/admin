<?php
$controller = $this->request->controller;
$modelParam = $this->request->model;
$pluginParam = null;

if ($modelParam) {
    list($pluginParam, $modelParam) = pluginSplit($modelParam);
} ?>

<nav class="nav clear-after">
    <div class="nav-buttons">
        <div class="button-group round">
            <button type="button" class="button last js-dropdown" data-dropdown="#nav-dropdown">
                <?php if (!empty($user[$config['User']['fieldMap']['avatar']])) {
                    echo $this->Html->image($user[$config['User']['fieldMap']['avatar']], array('class' => 'avatar'));
                } ?>

                <?php echo $user[$config['User']['fieldMap']['username']]; ?>
                <span class="caret-down"></span>
            </button>

            <ul class="dropdown push-over" id="nav-dropdown">
                <li><?php echo $this->Html->link(__d('admin', 'View Site'), '/'); ?></li>
                <?php
                if ($profileRoute = $this->Admin->getUserRoute('profile', $user)) { ?>
                    <li><?php echo $this->Html->link(__d('admin', 'View Profile'), $profileRoute); ?></li>
                <?php }
                if ($settingsRoute = $this->Admin->getUserRoute('settings', $user)) { ?>
                    <li><?php echo $this->Html->link(__d('admin', 'Settings'), $settingsRoute); ?></li>
                <?php } ?>
            </ul>
        </div>

        <?php echo $this->Html->link(__d('admin', 'Logout'), $config['User']['routes']['logout'], array('class' => 'button is-error')); ?>
    </div>

    <?php // Brand name
    $navTitle = $config['Admin']['appName'];

    if (env('REMOTE_ADDR') === '127.0.0.1') {
        $navTitle .= ' <span class="label is-error">dev</span>';
    } else {
        $navTitle .= ' <span class="label is-success">prod</span>';
    }

    echo $this->Html->link($navTitle, array(
        'plugin' => 'admin',
        'controller' => 'admin',
        'action' => 'index'
    ), array('class' => 'nav-brand', 'escape' => false)); ?>

    <ul class="nav-menu">
        <?php // Loop top-level menu
        $currentRoute = Router::currentRoute();
        $currentSection = empty($currentRoute->options['section']) ? null : $currentRoute->options['section'];

        foreach (Configure::read('Admin.menu') as $section => $menu) { ?>

            <li<?php echo ($currentSection === $section) ? ' class="is-active"' : ''; ?>>
                <?php
                $title = $menu['title'];

                if (!empty($badgeCounts[$section])) {
                    $title .= ' <span class="label--badge is-warning">' . $badgeCounts[$section] . '</span>';
                }

                echo $this->Html->link($title, $menu['url'], array('escape' => false)); ?>
            </li>

        <?php }

        // Loop model menu
        foreach ($this->Admin->getNavigation() as $plugin => $groups) {
            if (empty($groups)) {
                continue;
            }

            $pluginLower = strtolower($plugin); ?>

            <li<?php echo ($pluginLower === $pluginParam) ? ' class="is-active"' : ''; ?>>
                <a href="javascript:;" class="js-dropdown" data-dropdown="#nav-<?php echo $pluginLower; ?>">
                    <?php echo $plugin; ?>
                    <span class="caret-down"></span>
                </a>

                <ul class="dropdown" id="nav-<?php echo $pluginLower; ?>">
                    <?php // Single group
                    if (count($groups) == 1) {
                        $groups = array_values($groups);

                        foreach ($groups[0] as $model) { ?>

                            <li>
                                <?php echo $this->Html->link($this->Admin->outputIconTitle($model), array(
                                    'plugin' => 'admin',
                                    'controller' => 'crud',
                                    'action' => 'index',
                                    'model' => $model['url']
                                ), array('escape' => false)); ?>
                            </li>

                        <?php }

                    // Multiple groups
                    } else {
                        foreach ($groups as $group => $models) { ?>

                            <li class="has-children">
                                <a href="javascript:;">
                                    <?php echo $group; ?>
                                    <span class="caret-right"></span>
                                </a>

                                <ul class="dropdown">
                                    <?php foreach ($models as $model) { ?>

                                        <li>
                                            <?php echo $this->Html->link($this->Admin->outputIconTitle($model), array(
                                                'plugin' => 'admin',
                                                'controller' => 'crud',
                                                'action' => 'index',
                                                'model' => $model['url']
                                            ), array('escape' => false)); ?>
                                        </li>

                                    <?php } ?>
                                </ul>
                          </li>

                    <?php } } ?>
                </ul>
            </li>

        <?php } ?>
    </ul>
</nav>