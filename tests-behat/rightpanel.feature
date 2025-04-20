Feature: RightPanel

  Scenario: PanelReload
    Given I am on "layout/layout-panel.php"
    When I press button "Button 1"
    Then I should see "button #1"
    When I press button "Reload Myself"
    When I press button "Complete"
    Then I should see "Completed using button #1"

  Scenario: PanelModelAction
    Given I am on "layout/layout-panel.php"
    When I click using selector "(//div.atk-card)[1]"
    When I press button "User Confirmation"
    When I press Modal button "Ok"
    Then Toast display should contain text "Confirm country"
