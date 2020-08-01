
Feature: RightPanel
  Testing RightPanel

  Scenario: PanelReload
    Given I am on "layout/layout-panel.php"
    And I press button "Button 1"
    And wait for callback
    Then I should see "button #1"
    Then I press button "Reload Myself"
    And wait for callback
    Then I press button "Complete"
    And wait for callback
    Then I should see "Complete using button #1"

  Scenario: PanelModelAction
    Given I am on "layout/layout-panel.php"
    Then I click first card on page
    And wait for callback
    And I press button "User Confirmation"
    And wait for callback
    And I press Modal button "Ok"
    And wait for callback
    Then Toast display should contains text "Confirm country"
