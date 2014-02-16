<?php echo $this->Html->docType(); ?>
<html lang="en">
<head>
    <?php echo $this->Html->charset(); ?>
    <title><?php echo $this->Breadcrumb->pageTitle($config['Admin']['appName'], array('reverse' => true)); ?></title>
    <?php
    echo $this->Html->css('Admin.titon.min');
    echo $this->Html->css('Admin.font-awesome.min');
    echo $this->Html->css('Admin.style');
    echo $this->Html->css('Admin.admin');
    echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
    echo $this->Html->script('Admin.titon.min');
    echo $this->Html->script('Admin.admin');
    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script'); ?>
</head>
<body class="controller-<?php echo $this->request->controller; ?>">
    <div class="skeleton">
        <header class="head">
            <?php echo $this->element('Admin.navigation'); ?>
        </header>

        <div class="body action-<?php echo $this->action; ?>">
            <?php
            $this->Breadcrumb->prepend(__d('admin', 'Dashboard'), array('controller' => 'admin', 'action' => 'index'));

            echo $this->element('Admin.breadcrumbs');
            echo $this->Session->flash();
            echo $this->fetch('content'); ?>
        </div>

        <footer class="foot">
            <div class="copyright">
                <?php printf(__d('admin', 'Powered by the %s v%s'), $this->Html->link('Admin Plugin', 'http://milesj.me/code/cakephp/admin'), mb_strtoupper($config['Admin']['version'])); ?><br>
                <?php printf(__d('admin', 'Created by %s'), $this->Html->link('Miles Johnson', 'http://milesj.me')); ?>
            </div>

            <?php if (!CakePlugin::loaded('DebugKit')) {
                echo $this->element('sql_dump');
            } ?>
        </footer>
    </div>
</body>
</html>