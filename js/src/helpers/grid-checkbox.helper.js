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
            // check all children
            onChecked: function () {
                const $childCheckbox = $(this).closest('.table').find('.child.checkbox');
                $childCheckbox.checkbox('check');
            },
            // uncheck all children
            onUnchecked: function () {
                const $childCheckbox = $(this).closest('.table').find('.child.checkbox');
                $childCheckbox.checkbox('uncheck');
            },
        });
    },

    childCheckbox: function () {
        $('.table .child.checkbox').checkbox({
            // Fire on load to set parent value
            fireOnInit: true,

            // Change parent state on each child checkbox change
            onChange: function () {
                const $listGroup = $(this).closest('.table');
                const $parentCheckbox = $listGroup.find('.master.checkbox');
                const $checkbox = $listGroup.find('.child.checkbox');
                let allChecked = true;
                let allUnchecked = true;

                // check to see if all other siblings are checked or unchecked
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
