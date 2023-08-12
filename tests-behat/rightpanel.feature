Feature: RightPanel

  Scenario: PanelReload
    Given I am on "layout/layout-panel.php"
    When I press button "Button 1"
    Then I should see "button #1"
    Then I press button "Reload Myself"
    Then I press button "Complete"
    Then I should see "Complete using button #1"

  Scenario: PanelModelAction
    Given I am on "layout/layout-panel.php"
    Then I click using selector "(//div.atk-card)[1]"
    When I press button "User Confirmation"
    When I press Modal button "Ok"
    Then Toast display should contain text "Confirm country"
