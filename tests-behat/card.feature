Feature: Card

  Scenario:
    Given I am on "interactive/card-action.php"
    When I press button "Send Note"
    Then Modal is open with text "Note" in tag "label"
    When I fill in "note" with "This is a test note"
    Then I press Modal button "Notify"
    Then Toast display should contain text "This is a test note"
