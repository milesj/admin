<?php if (empty($model->enum[$field][$value])) { ?>

	<span class="enum text-error">INVALID_ENUM</span>

<?php } else {
	$enum = $model->enum[$field][$value]; ?>

	<span class="enum enum-<?php echo strtolower($enum); ?>"><?php echo $enum; ?></span>

<?php } ?>