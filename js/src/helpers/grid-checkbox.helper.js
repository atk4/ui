import $ from 'external/jquery';

export default {
    /**
     * Simple helper to help displaying Fomantic-UI checkbox within an atk grid.
     * The master checkbox in the header of the column enable to toggle all
     * content checkboxes to check or uncheck. A partially checked master checkbox
     * is displayed if appopriate.
     */
    masterCheckbox: function () {
        $('.table .master.checkbox').checkbox({
            onChecked: function () {
                // check all children
                const $childCheckbox = $(this).closest('.table').find('.child.checkbox');
                $childCheckbox.checkbox('check');
            },

            onUnchecked: function () {
                // uncheck all children
                const $childCheckbox = $(this).closest('.table').find('.child.checkbox');
                $childCheckbox.checkbox('uncheck');
            },
        });
    },

    childCheckbox: function () {
        $('.table .child.checkbox').checkbox({
            // fire on load to set parent value
            fireOnInit: false,

            // change parent state on each child checkbox change
            onChange: function () {
                const $listGroup = $(this).closest('.table');
                const $parentCheckbox = $listGroup.find('.master.checkbox');
                const $checkbox = $listGroup.find('.child.checkbox');

                // check to see if all other siblings are checked or unchecked
                let allChecked = true;
                let allUnchecked = true;
                $checkbox.each(function () {
                    if ($(this).checkbox('is checked')) {
                        allUnchecked = false;
                    } else {
                        allChecked = false;
                    }
                });

                // set parent checkbox state, but don't trigger its onChange callback
                if (allChecked) {
                    $parentCheckbox.checkbox('set checked');
                } else if (allUnchecked) {
                    $parentCheckbox.checkbox('set unchecked');
                } else {
                    $parentCheckbox.checkbox('set indeterminate');
                }
            },
        });
    },
};
