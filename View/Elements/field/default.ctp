<?php if ($value === null || $value === '') {
	echo '<div class="muted align-center">-</div>';

} else if (is_numeric($value)) {
	echo number_format($value);

} else {
	echo h($value);
}