<template>
    <div class="">
        <vue-query-builder :rules="rules" v-model="query">
            <template v-slot:default="slotProps">
                <query-builder-group v-bind="slotProps" :query.sync="query"/>
            </template>
        </vue-query-builder>

        <div class="my-6">---</div>

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
      data : {Object}
    },
    data() {
      return {
        rules: this.data.rules,
        query: this.data.query ? this.data.query : {},
        maxDepth: this.data.maxDepth ? this.data.maxDepth : 3,
      }
    }
  };
</script>
