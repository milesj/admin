/**
 * @copyright    Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license        http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link        http://milesj.me/code/cakephp/admin
 */

'use strict';

var Admin = {

    /**
     * Initialize global events.
     */
    initialize: function() {

        // Make table rows clickable
        $('table.is-clickable tbody tr').click(function(e) {
            var target = $(e.target),
                tag = target.prop('tagName').toLowerCase();

            if (tag === 'a' || tag === 'input') {
                return;
            }

            var id = target.parent('tr').find('.click-target');

            if (id.length) {
                location.href = id.attr('href');
            }
        });

        // Check all checkbox on tables
        $('#check-all').click(function() {
            $('#table').find('input:checkbox').prop('checked', this.checked);
        });

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
        var inputNull = $('#' + id + 'Null'),
            inputRaw = $('#' + id);

        $('#' + id + 'TypeAhead').typeAhead({
            sorter: false,
            matcher: false,
            shadow: true,
            source: url,
            query: data,
            onSelect: function(item) {
                inputRaw.val(item.id);
                inputNull.prop('checked', false);
            },
            onReset: function() {
                inputRaw.val('');
                inputNull.prop('checked', true);
            }
        });
    },

    /**
     * Monitor null input fields and toggle the checkbox depending on the input length.
     */
    nullChecks: function() {
        $('.field-null input[type="checkbox"]').each(function() {
            var cb = $(this),
                related = cb.parent().siblings('select, input');

            if (related.length) {
                var callback = function() {
                    cb.prop('checked', !(this.value.length));
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
     * Toggle the filters box and button.
     */
    filterToggle: function() {
        $('#filters').toggle();
        $('#filter-toggle').toggleClass('is-active');
    },

    /**
     * Allow filter comparison dropdowns to change input fields with the chosen option.
     */
    filterComparisons: function() {
        $('#filters').find('.input-group').each(function() {
            var group = $(this),
                filter = group.find('input[type="hidden"]'),
                button = group.find('button');

            group.find('ul a').click(function() {
                var option = $(this).data('filter');

                filter.val(option);
                button.text(option);
            });
        });
    },

    /**
     * Toggle the grouped input fields within the upload form.
     *
     * @param {Event} e
     */
    toggleUploadField: function(e) {
        var self = $(e.target),
            fieldset = self.parents('fieldset');

        fieldset.find(self.data('target')).hide();

        if (self.val()) {
            fieldset.find('.' + self.val()).show();
        }
    }

};

$(function() {
    Admin.initialize();

    $('.js-modal').modal({
        animation: 'slide-in-top'
    });

    $('.js-dropdown').dropdown();

    $('.js-tooltip').tooltip({
        position: 'topCenter'
    });

    $('.js-matrix').matrix({
        width: 400,
        gutter: 30,
        selector: '.panel'
    });

    $('form').input();
});