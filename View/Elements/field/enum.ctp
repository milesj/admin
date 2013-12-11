<?php
$class = 'type enum enum-' . $field .' enum-' . $field . '-' . $value;

if (is_numeric($value)) {
    if (empty($model->enum[$field][$value])) { ?>

        <span class="type enum text-error">INVALID ENUM: <?php echo $value; ?></span>

    <?php } else { ?>

        <span class="<?php echo $class; ?>">
            <?php echo $this->Utility->enum($model->qualifiedName, $field, $value); ?>
        </span>

    <?php }
} else { ?>

    <span class="<?php echo $class; ?>"><?php echo $value; ?></span>

<?php } ?>