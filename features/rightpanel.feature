
Feature: RightPanel
  Testing RightPanel

  Scenario:
    Given I am on "layout/layout-panel.php"
    And I press button "Button 1"
    And wait for callback
    Then I should see "button #1"
    Then I press button "Reload Myself"
    And wait for callback
    Then I press button "Complete"
    And wait for callback
    Then I should see "Complete using button #1"
