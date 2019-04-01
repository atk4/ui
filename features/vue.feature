Feature: Vue
  In order to have an awesome PHP UI Framework
  As a responsible open-source developer
  I need to write tests for our demo pages

  Scenario:
    Given I am on "vue-component.php"
    When I fill in "name" with "test"
    And form submits
    Then I should see "new value: test"

  Scenario:
    Given I am on "vue-component.php"
    When I fill in "atk-vue-search" with "united kingdom"
    Then I wait for send action using ".atk-item-search.loading"
    And form submits
    Then I should see "United Kingdom"

