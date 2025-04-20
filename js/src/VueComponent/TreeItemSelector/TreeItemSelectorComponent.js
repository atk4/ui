import $ from 'external/jquery';

export default {
    name: 'AtkTreeItemSelector',
    template: `
        <div class="item" :style="itemMargin">
            <i :class="toggleIcon" v-show="!isRoot" @click="onToggleShow" />
            <i :class="getIcon" v-show="!isRoot" @click="onToggleSelect" />
            <div class="content">
                <div :style="itemCursor" @click="onToggleSelect">{{title}}</div>
                <div v-if="isParent" class="list" v-show="open || isRoot">
                    <AtkTreeItemSelector
                        v-for="item in item.nodes" :key="item.id"
                        :item="item"
                        :values="values"
                    ></AtkTreeItemSelector>
                </div>
            </div>
        </div>`,
    props: { item: Object, values: Array },
    inject: ['getRootData'],
    data: function () {
        return {
            open: false,
            isRoot: this.item.id === 'atk-root',
            isInitialized: false,
            id: this.item.id,
            nodes: this.item.nodes,
            icons: {
                single: {
                    on: 'circle',
                    off: 'circle outline',
                    indeterminate: 'dot circle outline',
                },
                multiple: {
                    on: 'check square outline',
                    off: 'square outline',
                    indeterminate: 'minus square outline',
                },
            },
        };
    },
    created: function () {
        this.getInitData();
    },
    mounted: function () {},
    computed: {
        itemMargin: function () {
            return {
                marginLeft: this.item.nodes && this.item.nodes.length > 0
                    ? (this.open ? '-13px' : '-10px')
                    : null,
            };
        },
        itemCursor: function () {
            return {
                cursor: this.isParent && this.getRootData().options.mode === 'single' ? 'default' : 'pointer',
            };
        },
        title: function () {
            return this.item.name;
        },
        isParent: function () {
            return this.nodes && this.nodes.length > 0;
        },
        toggleIcon: function () {
            return this.isParent
                ? (this.open ? 'caret down' : 'caret right') + ' icon'
                : null;
        },
        state: function () {
            let state = 'off';
            if (this.isParent) {
                state = this.hasAllFill(this.nodes)
                    ? 'on'
                    : (this.hasSomeFill(this.nodes) ? 'indeterminate' : 'off');
            } else if (this.isSelected(this.id)) {
                state = 'on';
            }

            return state;
        },
        getIcon: function () {
            return this.icons[this.getRootData().options.mode][this.state] + ' icon';
        },
    },
    methods: {
        isSelected: function (id) {
            return this.values.includes(id);
        },
        /**
         * Get input initial data.
         */
        getInitData: function () {
            // check if input containing data is set and initialized
            if (!this.getRootData().item.isInitialized) {
                this.getRootData().values = this.getValues();
                this.getRootData().item.isInitialized = true;
            }
        },
        getValues: function () {
            const initValues = JSON.parse(this.getInputElement().value);
            let values = [];
            if (Array.isArray(initValues)) {
                values = initValues;
            } else {
                values.push(initValues);
            }

            return values;
        },
        /**
         * Check if all children nodes are on.
         *
         * @returns {boolean}
         */
        hasAllFill: function (nodes) {
            let state = true;
            for (const node of nodes) {
                // check children first;
                if (node.nodes && node.nodes.length > 0) {
                    if (!this.hasAllFill(node.nodes)) {
                        state = false;

                        break;
                    }
                } else if (!this.values.includes(node.id)) {
                    state = false;

                    break;
                }
            }

            return state;
        },
        /**
         * Check if some children nodes are on.
         *
         * @returns {boolean}
         */
        hasSomeFill: function (nodes) {
            let state = false;
            for (const node of nodes) {
                // check children first;
                if (node.nodes && node.nodes.length > 0) {
                    if (this.hasSomeFill(node.nodes)) {
                        state = true;

                        break;
                    }
                }
                if (this.values.includes(node.id)) {
                    state = true;

                    break;
                }
            }

            return state;
        },
        /**
         * Fire when arrow are click in order to show or hide children.
         */
        onToggleShow: function () {
            if (this.isParent) {
                this.open = !this.open;
            }
        },
        /**
         * Fire when checkbox is click.
         */
        onToggleSelect: function () {
            const { options } = this.getRootData();
            switch (options.mode) {
                case 'single': {
                    this.handleSingleSelect();

                    break;
                }
                case 'multiple': {
                    this.handleMultipleSelect();

                    break;
                }
            }
        },
        /**
         * Merge array and remove duplicate.
         *
         * @returns {*[]}
         */
        mergeArrays: function (...arrays) {
            let jointArray = [];
            for (const array of arrays) {
                jointArray = [...jointArray, ...array];
            }

            return [...new Set(jointArray)];
        },
        /**
         * Get all ID from all children node.
         *
         * @returns {Array.<string>}
         */
        collectAllChildren: function (nodes, ids = []) {
            for (const node of nodes) {
                if (node.nodes && node.nodes.length > 0) {
                    ids = [...ids, ...this.collectAllChildren(node.nodes, ids)];
                } else {
                    ids.push(node.id);
                }
            }

            return ids;
        },
        remove: function (values, value) {
            return values.filter((val) => val !== value);
        },
        /**
         * Handle a selection when in single mode.
         */
        handleSingleSelect: function () {
            if (this.state === 'off' && !this.isParent) {
                this.getRootData().values = [this.item.id];
                this.setInput(this.item.id);
                if (this.getRootData().options.url) {
                    this.postValue();
                }
            }
            if (this.isParent) {
                this.open = !this.open;
            }
        },
        /**
         * Handle a selection when in multiple mode.
         */
        handleMultipleSelect: function () {
            let values;
            if (this.isParent) {
                // collect all children value
                const childValues = this.collectAllChildren(this.nodes);
                if (this.state === 'off' || this.state === 'indeterminate') {
                    values = this.mergeArrays(this.values, childValues);
                } else {
                    let temp = this.values;
                    for (const value of childValues) {
                        temp = this.remove(temp, value);
                    }
                    values = temp;
                }
            } else if (this.state === 'on') {
                values = this.remove(this.values, this.item.id);
            } else if (this.state === 'off') {
                values = this.values;
                values.push(this.item.id);
            }

            this.getRootData().values = [...values];
            this.setInput(JSON.stringify(values));

            if (this.getRootData().options.url) {
                this.postValue();
            }
        },
        /**
         * Set input field with current mapped model value.
         */
        setInput: function (value) {
            this.getInputElement().value = value;
        },
        /**
         * Get input element set for this Item Selector.
         *
         * @returns {HTMLElement}
         */
        getInputElement: function () {
            return document.getElementsByName(this.getRootData().field)[0];
        },
        /**
         * Send data using callback URL.
         */
        postValue: function () {
            $(this.$el).parents('.' + this.getRootData().options.loader).api({
                on: 'now',
                url: this.getRootData().options.url,
                method: 'POST',
                data: { data: JSON.stringify(this.getRootData().values) },
            });
        },
    },
};
