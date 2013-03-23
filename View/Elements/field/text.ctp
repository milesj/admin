<?php
// Display full data on read
if ($this->action === 'read') {
	if (is_array($value)) {
		echo $this->element('Admin.field/array', array('value' => $value));

	} else {
		echo nl2br(h($value));
	}

// Else show trimmed version
} else {
	if (is_array($value)) { ?>

		<span class="text-warning">SERIALIZED</span>

	<?php } else {
		echo $this->Text->truncate(h($value), 100);
	}
}