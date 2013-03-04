<?php
if (empty($typeAhead[$field])) {
	echo $this->Form->input($field, array(
		'div' => false,
		'label' => false
	));

} else {
	echo $this->Form->input($field, array('type' => 'hidden'));
	echo $this->Form->input($field . '_type_ahead', array(
		'div' => false,
		'label' => false,
		'type' => 'text',
		'class' => 'span2',
		'data-provide' => 'typeahead',
		'autocomplete' => 'off',
		'value' => $this->data[$typeAhead[$field]['alias']][$model->{$typeAhead[$field]['alias']}->displayField]
	));

	$id = $this->Form->domId(); ?>

	<script type="text/javascript">
		$(function() {
			$('#<?php echo $id; ?>').typeahead({
				items: 15,
				minLength: 2,
				source: function(query, process) {
					return $.ajax({
						url: '<?php echo $this->Html->url(array('model' => $this->params['model'], 'action' => 'type_ahead')); ?>',
						type: 'get',
						data: {
							query: query,
							model: '<?php echo $typeAhead[$field]['model']; ?>'
						},
						dataType: 'json',
						success: function(json) {
							var map = {},
								source = [];

							$.each(json, function(k, v) {
								map[v] = k;
								source.push(v);
							});

							$('#<?php echo $id; ?>').data('typeahead', map);

							return process(source);
						}
					});
				},
				updater: function(item) {
					var map = $('#<?php echo $id; ?>').data('typeahead');

					$('#<?php echo str_replace('TypeAhead', '', $id); ?>').val(map[item]);

					return item;
				}
			});
		});
	</script>
<?php }