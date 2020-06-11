Feature: Card
  Testing card with model action

    Scenario:
    Given I am on "interactive/card-action.php"
    And I press button "Send Note"
    And wait for callback
    Then Modal is showing text "Note" inside tag "label"
    When I fill in "note" with "This is a test note"
    Then I press Modal button "Notify"
    And wait for callback
    Then Toast display should contains text "This is a test note"
