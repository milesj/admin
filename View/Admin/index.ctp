<?php if (!$this->Session->check('Admin')) { ?>
    <div class="notice is-error">
        <span class="fa fa-warning-sign"></span> &nbsp;
        <?php echo __d('admin', 'ACL permissions do not exist within the session and all access has been disabled.'); ?>
        <a href="http://milesj.me/code/cakephp/admin#faq" class="notice-link"><?php echo __d('admin', 'Please update session management and re-login.'); ?></a>
    </div>
<?php } ?>

<div class="title">
    <?php echo $this->element('admin/actions'); ?>

    <h2><?php echo __d('admin', 'Plugins'); ?></h2>
</div>

<div class="container">
    <div class="panels js-matrix">

    <?php foreach ($plugins as $plugin) { ?>

        <div class="panel" id="<?php echo $plugin['slug']; ?>">
            <div class="panel-head">
                <h4><?php echo $plugin['title']; ?></h4>
            </div>

            <div class="panel-body">
                <ul class="plugin-list">
                    <?php foreach ($plugin['models'] as $model) {
                        $url = $this->Html->url(array(
                            'controller' => 'crud',
                            'action' => 'index',
                            'model' => $model['url']
                        )); ?>

                        <li>
                            <?php if ($model['installed']) {
                                echo $this->Html->link('<span class="fa fa-plus"></span>', array(
                                    'controller' => 'crud',
                                    'action' => 'create',
                                    'model' => $model['url']
                                ), array(
                                    'data-tooltip' => __d('admin', 'Add'),
                                    'class' => 'float-right js-tooltip',
                                    'escape' => false
                                ));
                            } ?>

                            <a href="<?php echo $url; ?>">
                                <?php echo $this->Admin->outputIconTitle($model); ?>

                                <?php if (!$model['installed']) { ?>
                                    <span class="label is-error js-tooltip" data-tooltip="<?php echo __d('admin', 'Not Installed'); ?>">&nbsp;!&nbsp;</span>
                                <?php } else { ?>
                                    <span class="text-muted">(<?php echo number_format($counts[$model['class']]); ?>)</span>
                                <?php } ?>
                            </a>
                        </li>

                    <?php } ?>
                </ul>
            </div>
        </div>

    <?php } ?>

    </div>
</div>