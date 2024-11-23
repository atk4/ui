import $ from 'external/jquery';

function recomputeMasterCheckbox($table) {
    const $masterCheckbox = $table.find('.master.checkbox');
    const $childCheckbox = $table.find('.child.checkbox');

    const checkedCount = $childCheckbox.filter('.checked').length;
    let allChecked = checkedCount === $childCheckbox.length;
    let allUnchecked = checkedCount === 0;

    if (allChecked) {
        $masterCheckbox.checkbox('set checked');
    } else if (allUnchecked) {
        $masterCheckbox.checkbox('set unchecked');
    } else {
        $masterCheckbox.checkbox('set indeterminate');
    }
};

export default {
    /**
     * Simple helper to help displaying Fomantic-UI checkbox within an atk grid.
     * The master checkbox in the header of the column enable to toggle all
     * content checkboxes to check or uncheck. A partially checked master checkbox
     * is displayed if appopriate.
     */
    setupMasterCheckbox: function (tableSelector) {
        let $table = $(tableSelector);
        let skipRecomputeMasterCheckbox = false;

        $table.find('.master.checkbox').checkbox({
            onChecked: function () {
                const $childCheckbox = $table.find('.child.checkbox');

                skipRecomputeMasterCheckbox = true;
                try {
                    $childCheckbox.checkbox('check');
                } finally {
                    skipRecomputeMasterCheckbox = false;
                }
            },

            onUnchecked: function () {
                const $childCheckbox = $table.find('.child.checkbox');

                skipRecomputeMasterCheckbox = true;
                try {
                    $childCheckbox.checkbox('uncheck');
                } finally {
                    skipRecomputeMasterCheckbox = false;
                }
            },
        });

        $table.find('.child.checkbox').checkbox({
            onChange: function () {
                if (skipRecomputeMasterCheckbox) {
                    return;
                }

                recomputeMasterCheckbox($table);
            },
        });
    },
};
