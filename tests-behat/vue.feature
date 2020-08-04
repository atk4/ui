Feature: Vue
  Testing Vue component

  Scenario: testing InlineEdit
    Given I am on "javascript/vue-component.php"
    When I fill in "name" with "test"
    Then I should see "new value: test"
    Then I hide js modal

    When I fill in "atk-vue-search" with "united kingdom"
# needed because debouce is used
# see https://github.com/atk4/ui/blob/22e00dfc72506aff63ba09585a9e12b71bc046ec/js/src/components/item-search.component.js#L42
# see https://github.com/component/debounce/blob/cceb38c9a4f4b3d628b6fa28692fe575611c45f6/index.js#L15
    Then I sleep 300 ms
    Then I should see "United Kingdom"
