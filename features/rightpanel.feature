
Feature: RightPanel
  In order to have an awesome PHP UI Framework
  As a responsible open-source developer
  I need to write tests for our demo pages

  Scenario:
    Given I am on "layout-panel.php"
    And I press button "Button 1"
    And wait for callback
    Then I should see "button #1"
