
Feature: RightPanel
  Testing RightPanel

  Scenario:
    Given I am on "layout-panel.php"
    And I press button "Button 1"
    And wait for callback
    Then I should see "button #1"
