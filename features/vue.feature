Feature: Vue
  Testing Vue component

  Scenario: testing InlineEdit
    Given I am on "javascript/vue-component.php"
    When I fill in "name" with "test"
    And form submits
    And wait for callback
    Then I should see "new value: test"
    Then I hide js modal

    When I fill in "atk-vue-search" with "united kingdom"
    And wait for callback
    And form submits
    Then I should see "United Kingdom"
