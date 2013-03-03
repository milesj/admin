<?php if ($value === null || $value === '') { ?>

	<div class="muted align-center">-</div>

<?php } else if (is_numeric($value)) {
	echo number_format($value);

} else {
	echo $this->Text->truncate(h($value), 100);
}