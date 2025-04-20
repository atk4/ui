import $ from 'external/jquery';

function recomputeMasterCheckbox($table) {
    const $masterCheckbox = $table.find('.master.checkbox');
    const $childCheckbox = $table.find('.child.checkbox');

    const checkedCount = $childCheckbox.filter('.checked').length;
    const allChecked = checkedCount === $childCheckbox.length;
    const allUnchecked = checkedCount === 0;

    if (allChecked) {
        $masterCheckbox.checkbox('set checked');
    } else if (allUnchecked) {
        $masterCheckbox.checkbox('set unchecked');
    } else {
        $masterCheckbox.checkbox('set indeterminate');
    }
}

export default {
    /**
     * Simple helper for master and child checkboxes connection.
     */
    setupMasterCheckbox: function (tableSelector) {
        const $table = $(tableSelector);
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

    /**
     * Invoke callback on checked and indeterminate change.
     */
    onMasterCheckboxChange: function (tableSelector, fx) {
        const $table = $(tableSelector);
        const $masterCheckbox = $table.find('.master.checkbox');

        new MutationObserver(() => fx($masterCheckbox.first())).observe($masterCheckbox[0], {
            attributes: true,
            attributeFilter: ['class'],
        });
    },
};
