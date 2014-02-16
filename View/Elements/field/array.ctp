<?php if (empty($value)) {
    return;
} ?>

<ul class="type array">
    <?php foreach ($value as $key => $value) { ?>

        <li>
            <b><?php echo $key; ?>:</b>

            <?php if (is_array($value)) {
                echo $this->element('Admin.field/array', array('value' => $value));
            } else {
                echo h($value);
            } ?>
        </li>

    <?php } ?>
</ul>