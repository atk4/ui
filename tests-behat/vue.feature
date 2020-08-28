@vue-tag
Feature: Vue
  Testing Vue component

  @vue-inline-edit
  Scenario: testing InlineEdit
    Given I am on "javascript/vue-component.php"
    When I fill in "name" with "test"
    Then I should see "new value: test"
    Then I hide js modal

  @vue-item-search
  Scenario: testing ItemSearch
    Given I am on "_unit-test/item-search.php"
    When I fill in "atk-vue-search" with "united kingdom"
    Then I should see "United Kingdom"
