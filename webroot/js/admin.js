
var Admin = {

	/**
	 * Initialize global events.
	 */
	initialize: function() {
		$('.clickable tbody tr').click(function(e) {
			var target = $(e.target),
				tag = target.prop('tagName').toLowerCase();

			if (tag === 'a' || tag === 'input') {
				return;
			}

			var id = $(this).find('.col-id');

			if (id.length) {
				location.href = id.find('a:first').attr('href');
			}
		});

		$('#check-all').click(function() {
			$('#table').find('input:checkbox').prop('checked', $(this).prop('checked'));
		});

		Admin.nullChecks();
	},

	/**
	 * Initialize type ahead for belongs to input fields.
	 *
	 * @param {String} id
	 * @param {String} url
	 */
	typeAhead: function(id, url) {
		var sourceMap = {};

		$('#' + id).typeahead({
			items: 15,
			minLength: 2,
			source: function(query, process) {
				return $.ajax({
					url: url,
					type: 'get',
					data: { query: query },
					dataType: 'json',
					success: function(json) {
						var source = [],
							display;

						$.each(json, function(id, title) {
							// Display ID in front of title
							display = (id != title) ? id + ' - ' + title : title;

							sourceMap[display] = [id, title];
							source.push(title);
						});

						return process(source);
					}
				});
			},
			updater: function(item) {
				item = sourceMap[item];

				$('#' + id.replace('TypeAhead', '')).val(item[0]);

				return item[1];
			}
		});
	},

	/**
	 * Monitor null input fields and toggle the checkbox depending on the input length.
	 * Does not support date selects.
	 */
	nullChecks: function() {
		$('.controls-null input:checkbox').each(function() {
			var self = $(this),
				related = $('#' + self.attr('id').replace('Null', ''));

			if (related.length) {
				var callback = function() {
					self.prop('checked', !(this.value));
				};

				if (related.prop('tagName').toLowerCase() === 'input') {
					related.keyup(callback);
				} else {
					related.change(callback);
				}
			}
		});
	}

};

$(function() {
	Admin.initialize();

	$('.tip').tooltip({
		placement: 'top'
	});
});