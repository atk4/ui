Feature: Card

  Scenario: Card with entity, entity must not reload
    Given I am on "data-action/factory-view.php"
    Then I click using selector "i.eye.icon"
    Then Modal is open with text "Display Preview prior to run the action"
    # TODO fix
    # Then Modal is open with text "Previewing country France NO RELOAD"
    Then I press Modal button "Preview"
    Then Toast display should contain text "Success: Done previewing France"

  Scenario: Card with entity action, entity must not reload
    When I press button "Edit"
    Then Modal is open with text "Edit Country"
    Then I check if input value for ".modal.front input" match text "Czech Republic NO RELOAD"

  Scenario:
    Given I am on "interactive/card-action.php"
    When I press button "Send Note"
    Then Modal is open with text "Note" in selector "label"
    When I fill in "note" with "This is a test note"
    Then I press Modal button "Notify"
    Then Toast display should contain text "This is a test note"
