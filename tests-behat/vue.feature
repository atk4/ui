Feature: Vue
  Testing Vue component

  Scenario: loading components
    Given I am on "javascript/vue-component.php"
    Then I check for vue components to be load

  Scenario: testing InlineEdit
    When I fill in "name" with "test"
    Then I should see "new value: test"
    Then I hide js modal

  Scenario: testing ItemSearch
    When I fill in "atk-vue-search" with "united kingdom"
    Then I should see "United Kingdom"
