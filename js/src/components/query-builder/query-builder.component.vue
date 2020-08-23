<template>
    <div class="">
        <input :form="form" :name="name" type="hidden" :value="value">
        <vue-query-builder :rules="rules" v-model="query" :maxDepth="maxDepth" :labels="labels">
            <template v-slot:default="slotProps">
                <query-builder-group v-bind="slotProps" :query.sync="query"/>
            </template>
        </vue-query-builder>
        <template v-if="debug">
            <pre>{{ JSON.stringify(this.query, null, 2) }}</pre>
        </template>
    </div>
</template>

<script>
  import VueQueryBuilder from "vue-query-builder";
  import QueryBuilderGroup from "./fomantic-ui-group.component.vue";

  export default {
    name: "query-builder",
    components: {
      VueQueryBuilder,
      QueryBuilderGroup
    },
    props : {
      data: Object,
    },
    data() {
      return {
        query: this.data.query ? this.data.query : {},
        rules: this.data.rules ? this.data.rules : [],
        name: this.data.name ? this.data.name : '',
        maxDepth: this.data.maxDepth ? ((this.data.maxDepth <= 10) ? this.data.maxDepth : 10 ): 1,
        labels: this.getLabels(this.data.labels),
        form: this.data.form,
        debug: this.data.debug ? this.data.debug : false,
      }
    },
    computed: {
      value: function() {
        return JSON.stringify(this.query, null);
      }
    },
    methods: {
      /**
       * Return default label and option.
       *
       * @param labels
       * @returns {any}
       */
      getLabels: function(labels) {
        labels = labels ? labels : {};

        return Object.assign({
          matchType: "Match Type",
          matchTypes: [
            {id: "AND", label: "And"},
            {id: "OR", label: "Or"}
          ],
          addRule: "Add Rule",
          removeRule: "small icon times",
          addGroup: "Add Group",
          removeGroup: "small icon times",
          textInputPlaceholder: "value",
          spaceRule: "fitted", // can be fitted, compact or padded.
          hiddenOperator: ['is empty', 'is not empty'], // a list of operators that when select, will hide user input.
        }, labels);
      },
    }
  };
</script>
