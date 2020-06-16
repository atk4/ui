<template>
    <div class="">
        <input :name="name" type="hidden" :value="value">
        <vue-query-builder :rules="rules" v-model="query" :maxDepth="maxDepth" :labels="labels">
            <template v-slot:default="slotProps">
                <query-builder-group v-bind="slotProps" :query.sync="query"/>
            </template>
        </vue-query-builder>

        <div>---</div>

        <p>Generated output:</p>

        <pre>{{ JSON.stringify(this.query, null, 2) }}</pre>
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
        maxDepth: this.data.maxDepth ? ((this.data.maxDepth <= 5) ? this.data.maxDepth : 5 ): 1,
        labels: this.getLabels(this.data.labels),
      }
    },
    computed: {
      value: function() {
        return JSON.stringify(this.query, null);
      }
    },
    methods: {
      getLabels: function(labels) {
        labels = labels ? labels : {};

        return Object.assign({
          "matchType": "Match Type",
          "matchTypes": [
            {"id": "all", "label": "And"},
            {"id": "any", "label": "Or"}
          ],
          "addRule": "Add Rule",
          "removeRule": "&times;",
          "addGroup": "Add Group",
          "removeGroup": "&times;",
          "textInputPlaceholder": "value",
        }, labels);
      },
    }
  };
</script>
