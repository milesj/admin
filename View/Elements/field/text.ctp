<?php
// Display full data on read
if ($this->action === 'read') {
    if (is_array($value)) {
        echo $this->element('Admin.field/array', array('value' => $value));

    } else { ?>

        <span class="type text"><?php echo nl2br(h($value)); ?></span>

    <?php }
// Else show trimmed version
} else {
    if (is_array($value)) { ?>

        <span class="text-warning">SERIALIZED</span>

    <?php } else { ?>

        <span class="type text"><?php echo $this->Text->truncate(h(strip_tags($value)), 100); ?></span>

    <?php }
}