<?php
$this->Breadcrumb->add(__d('admin', 'Logs'), array('controller' => 'logs', 'action' => 'index'));
$this->Breadcrumb->add(__d('admin', 'System'), array('controller' => 'logs', 'action' => 'read', $type));
$this->Breadcrumb->add(Inflector::humanize($type), array('controller' => 'logs', 'action' => 'read', $type)); ?>

<div class="title">
    <?php echo $this->element('logs/actions'); ?>

    <h2><?php echo __d('admin', 'System Logs'); ?></h2>
</div>

<div class="container">
    <table id="table" class="table">
        <thead>
            <tr>
                <th><span><?php echo __d('admin', 'Code'); ?></span></th>
                <th><span><?php echo __d('admin', 'Exception'); ?></span></th>
                <th><span><?php echo __d('admin', 'URL'); ?></span></th>
                <th><span><?php echo __d('admin', 'Message'); ?></span></th>
                <th><span><?php echo __d('admin', 'Count'); ?></span></th>
                <th><span><?php echo __d('admin', 'Date'); ?></span></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($logs) {
                foreach ($logs as $i => $log) { ?>

                <tr>
                    <td class="type-integer">
                        <?php
                        $class = null;
                        $text = null;

                        // Via fatal
                        if (mb_stripos($log['message'], 'fatal') !== false) {
                            $class = 'is-error';
                            $text = 'FATAL';

                        // Via exception
                        } else if ($log['exception']) {
                            $ex = new $log['exception'](null);
                            $text = $ex->getCode();

                            // Hide 0 status codes
                            if ($text == 0) {
                                $text = null;
                            }

                            if ($ex instanceof HttpException) {
                                $class = 'is-warning';

                            } else if ($ex instanceof FatalErrorException) {
                                $class = 'is-error';
                                $text = 'FATAL';

                            } else if ($ex instanceof CakeException) {
                                $class = 'is-info';
                            }
                        }

                        if ($text) { ?>

                            <span class="label <?php echo $class; ?>"><?php echo $text; ?></span>

                        <?php } ?>
                    </td>
                    <td><?php echo $log['exception']; ?></td>
                    <td><?php echo $log['url']; ?></td>
                    <td>
                        <?php if (!$log['stack']) {
                            echo $log['message'];
                        } else { ?>
                            <a href="javascript:;" onclick="$('#stack-<?php echo $i; ?>').toggle();"><?php echo strip_tags($log['message']); ?></a>

                            <div id="stack-<?php echo $i; ?>" class="text-muted" style="display: none;">
                                <?php echo implode('<br>', $log['stack']); ?>
                            </div>
                        <?php } ?>
                    </td>
                    <td class="type-integer"><?php echo $log['count']; ?></td>
                    <td class="type-datetime"><?php echo $log['date']; ?></td>
                </tr>

                <?php }
            } else { ?>

                <tr>
                    <td colspan="6" class="no-results">
                        <?php echo __d('admin', 'No results to display'); ?>
                    </td>
                </tr>

            <?php } ?>
        </tbody>
    </table>
</div>