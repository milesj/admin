
var Admin = {

	/**
	 * Initialize global events.
	 */
	initialize: function() {
		var el;

		// Add button class to pagination links since CakePHP doesn't support it
		$$('.pagination a').addClass('button');

		// Make table rows clickable
		$$('.table--clickable tbody tr').addEvent('click', function(e) {
			var target = $(e.target),
				tag = target.get('tag').toLowerCase();

			if (tag === 'a' || tag === 'input') {
				return;
			}

			var id = $(this).getElement('.click-target');

			if (id) {
				location.href = id.get('href');
			}
		});

		// Check all checkbox on tables
		if (el = $('check-all')) {
			el.addEvent('click', function() {
				$('table').getElements('input[type="checkbox"]').set('checked', this.checked);
			});
		}

		// Trigger null checks for forms
		Admin.nullChecks();
	},

	/**
	 * Initialize type ahead for belongs to input fields.
	 *
	 * @param {String} id
	 * @param {String} url
	 * @param {Object} data
	 */
	typeAhead: function(id, url, data) {
		var inputNull = $(id + 'Null'),
			inputRaw = $(id);

		Titon.TypeAhead.factory(id + 'TypeAhead', {
			sorter: false,
			matcher: false,
			shadow: true,
			source: url,
			query: data,
			onSelect: function(item) {
				inputRaw.set('value', item.id);

				if (inputNull) {
					inputNull.set('checked', false);
				}
			},
			onReset: function() {
				inputRaw.set('value', '');

				if (inputNull) {
					inputNull.set('checked', true);
				}
			}
		});
	},

	/**
	 * Monitor null input fields and toggle the checkbox depending on the input length.
	 */
	nullChecks: function() {
		$$('.field-null input[type="checkbox"]').each(function(cb) {
			var related = cb.getParent().getSiblings('select, input');

			if (related) {
				var callback = function() {
					this.set('checked', !(this.value.length));
				}.bind(cb);

				if (related.get('tag') === 'input') {
					related.addEvent('keyup', callback);
				} else {
					related.addEvent('change', callback);
				}
			}
		});
	},

	/**
	 * Toggle the filters box and button.
	 */
	filterToggle: function() {
		$('filters').toggle();
		$('filter-toggle').toggleClass('is-active');
	},

	/**
	 * Allow filter comparison dropdowns to change input fields with the chosen option.
	 */
	filterComparisons: function() {
		$('filters').getElements('.input-prepend').each(function() {
			/*var self = $(this),
				filter = self.find('input[type="hidden"]'),
				button = self.find('button');

			self.find('ul a').click(function() {
				var option = $(this).data('filter');

				filter.val(option);
				button.text(option);
			});*/
		});
	},

	/**
	 * Toggle the grouped input fields within the upload form.
	 *
	 * @param {DOMEvent} e
	 */
	toggleUploadField: function(e) {
		var self = e.target,
			fieldset = self.getParent('fieldset');

		fieldset.getElements(self.get('data-target')).hide(true);
		fieldset.getElements('.' + self.get('value')).show(true);
	}

};

window.addEvent('domready', function() {
	Admin.initialize();

	Titon.Modal.factory('.js-modal', {
		animation: 'slide-in-top'
	});

	Titon.Toggle.factory('.js-toggle');

	Titon.Tooltip.factory('.js-tooltip', {
		animation: 'from-above',
		position: 'topCenter'
	});
});