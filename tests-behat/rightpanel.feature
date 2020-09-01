Feature: RightPanel
  Testing RightPanel

  Scenario: PanelReload
    Given I am on "layout/layout-panel.php"
    And I press button "Button 1"
    Then I should see "button #1"
    Then I press button "Reload Myself"
    Then I press button "Complete"
    Then I should see "Complete using button #1"

  Scenario: PanelModelAction
    Given I am on "layout/layout-panel.php"
    Then I click first card on page
    And I press button "User Confirmation"
    And I press Modal button "Ok"
    Then Toast display should contains text "Confirm country"
