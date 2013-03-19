
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

			var id = $(this).find('.click-target');

			if (id.length) {
				location.href = id.attr('href');
			}
		});

		$('#check-all').click(function() {
			$('#table').find('input[type="checkbox"]:not(:disabled)').prop('checked', $(this).prop('checked'));
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
		var sourceMap = {},
			inputTA = $('#' + id),
			inputRaw = $('#' + id.replace('TypeAhead', ''));

		inputTA.typeahead({
			items: 15,
			minLength: 1,
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
							//display = (id != title) ? id + ' - ' + title : title;
							display = title;

							sourceMap[display] = [id, title];
							source.push(display);
						});

						return process(source);
					}
				});
			},
			updater: function(item) {
				item = sourceMap[item];

				inputRaw.val(item[0]);

				return item[1];
			}
		});

		// Reset raw input if type ahead is cleared
		inputTA.keyup(function() {
			if (!this.value) {
				inputRaw.val('');
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
	},

	/**
	 * Allow filter comparison dropdowns to change input fields with the chosen option.
	 */
	filterComparisons: function() {
		$('#filters').find('.input-prepend').each(function() {
			var self = $(this),
				filter = self.find('input[type="hidden"]'),
				button = self.find('button');

			self.find('ul a').click(function() {
				var option = $(this).data('filter');

				filter.val(option);
				button.text(option);
			});
		});
	}

};

$(function() {
	Admin.initialize();

	// Tooltips
	$('.tip').tooltip({
		placement: 'top'
	});

	// Grids
	$('#grid').gridalicious({
		width: 400,
		gutter: 0,
		selector: '.well',
		animationOptions: {
			complete: function() {
				$('#grid').find('> div:hidden').remove();
			}
		}
	});
});