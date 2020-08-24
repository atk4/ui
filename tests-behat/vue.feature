Feature: Vue
  Testing Vue component

  Scenario: testing InlineEdit
    Given I am on "javascript/vue-component.php"
    When I fill in "name" with "test"
    Then I should see "new value: test"
    Then I hide js modal

    When I fill in "atk-vue-search" with "united kingdom"
# needed because debouce is used
    Then I sleep 250 ms
    Then I should see "United Kingdom"
