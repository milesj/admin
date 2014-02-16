<?php
// Flatten an array once a specific depth is reached
$flatten = function($array) {
    $output = array();

    foreach ($array as $key => $value) {
        $return = $key . ': ';

        if (is_array($value)) {
            $return .= '[' . $flatten($value) . ']';
        } else {
            $return .= '"' . $value . '"';
        }

        $output[] = $return;
    }

    return implode('<br>', $output);
}; ?>

<table class="table">
    <tbody>
        <?php foreach ($data as $key => $value) { ?>
            <tr>
                <td><b><?php echo $key; ?></b></td>
                <td>
                    <?php if (is_bool($value)) { ?>

                        <span class="text-error"><?php echo $value ? 'true' : 'false'; ?></span>

                    <?php } else if (is_numeric($value)) { ?>

                        <span class="text-warning"><?php echo $value; ?></span>

                    <?php } else if (empty($value)) { ?>

                        <span class="text-muted">(empty)</span>

                    <?php } else if (is_string($value)) {
                        if (mb_strlen($value) >= 30 && strpos($value, ' ') === false) { ?>

                            <span class="text-success js-tooltip" data-tooltip="<?php echo h($value); ?>"><?php echo $this->Text->truncate($value, 30); ?></span>

                        <?php } else { ?>

                            <span class="text-success"><?php echo h($value); ?></span>

                        <?php }
                    } else if (is_array($value)) {
                        // List of values
                        if (Hash::numeric(array_keys($value))) { ?>

                            <span class="text-info"><?php echo implode(', ', $value); ?></span>

                        <?php // Hash map
                        } else if ($depth > 0) {
                            echo $this->element('admin/config', array(
                                'data' => $value,
                                'parent' => $parent . $key . '.',
                                'depth' => ($depth + 1)
                            ));

                        // Display table in modal
                        } else {
                            $id = rand(); ?>

                            <a href="#modal-<?php echo $id; ?>" class="js-modal">
                                <?php echo __d('admin', 'View'); ?>
                                <span class="fa fa-external-link" style="font-size: 10px"></span>
                            </a>

                            <div id="modal-<?php echo $id; ?>" style="display: none;">
                                <div class="modal-head">
                                    <h3><?php echo $parent . $key; ?></h3>
                                </div>
                                <div class="modal-body">
                                    <?php echo $this->element('admin/config', array(
                                        'data' => $value,
                                        'parent' => $parent . $key . '.',
                                        'depth' => ($depth + 1)
                                    )); ?>
                                </div>
                                <div class="modal-foot">
                                    <button type="button" class="button modal-event-close"><?php echo __d('admin', 'Close'); ?></button>
                                </div>
                            </div>

                        <?php }
                    } ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>