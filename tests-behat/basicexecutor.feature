Feature: Executor
  Testing basic action executor

  Scenario: basic
    Given I am on "collection/actions.php"
    And I press button "Import"
    Then Toast display should contains text "Done!"

  Scenario: form
    Given I am on "collection/actions.php"
    And I press button "Run"
    Then I should see "Must not be empty"
    Then I fill in "path" with "."
    Then I press button "Run"
    Then Toast display should contains text "Imported!"

  Scenario: preview
    Given I am on "collection/actions.php"
    And I press button "Confirm"
    Then Toast display should contains text "Confirm!"
