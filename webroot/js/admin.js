
var Admin = {

	/**
	 * Initialize global events.
	 */
	initialize: function() {
		$('.clickable tbody tr').click(function() {
			var id = $(this).find('.col-id');

			if (id.length) {
				location.href = id.find('a:first').attr('href');
			}
		});

		$('#check-all').click(function() {
			$('#table').find('input:checkbox').prop('checked', $(this).prop('checked'));
		});
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
							source.push(display);
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
	}

};

$(function() {
	Admin.initialize();
});