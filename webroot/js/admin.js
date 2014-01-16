/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/admin
 */

'use strict';

var Admin = {

	/**
	 * Initialize global events.
	 */
	initialize: function() {
		var el;

		// Make table rows clickable
		$$('.table--clickable tbody tr').addEvent('click', function(e) {
			var target = e.target,
				tag = target.get('tag').toLowerCase();

			if (tag === 'a' || tag === 'input') {
				return;
			}

			var id = target.getParent('tr').getElement('.click-target');

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

		$(id + 'TypeAhead').typeAhead({
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
			var related = cb.getParent().getSiblings('select, input, textarea');

			if (related) {
				var callback = function() {
					cb.set('checked', !(this.value.length));
				};

				if (related.get('tag').toString() === 'input' || related.get('tag').toString() === 'textarea') {
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
		$('filters').getElements('.input-group').each(function(group) {
			var filter = group.getElements('input[type="hidden"]'),
				button = group.getElements('button');

			group.getElements('ul a').addEvent('click', function() {
				var option = this.get('data-filter');

				filter.set('value', option);
				button.set('text', option);
			});
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

		fieldset.getElements(self.get('data-target')).hide();
		fieldset.getElements('.' + self.get('value')).show();
	}

};

window.addEvent('domready', function() {
	Admin.initialize();

	$$('.js-modal').modal({
		animation: 'slide-in-top'
	});

	$$('.js-dropdown').dropdown();

	$$('.js-tooltip').tooltip({
		position: 'topCenter'
	});

    $$('.js-matrix').matrix({
        width: 400,
        gutter: 30,
        selector: '.panel'
    });
});