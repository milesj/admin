<?php
if (is_numeric($value)) {
	if (!isset($model->enum[$field][$value])) { ?>

		<span class="enum text-error">INVALID ENUM</span>

	<?php } else {
		$enum = $model->enum[$field][$value]; ?>

		<span class="enum enum-<?php echo strtolower($enum); ?>"><?php echo $enum; ?></span>

	<?php }
} else { ?>

	<span class="enum enum-<?php echo strtolower($value); ?>"><?php echo $value; ?></span>

<?php } ?>