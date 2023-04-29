Feature: Basic Executor

  Scenario: basic
    Given I am on "data-action/actions.php"
    When I press button "Import"
    Then Toast display should contain text "Done!"

  Scenario: form
    Given I am on "data-action/actions.php"
    When I press button "Run Import"
    Then I should see "Must not be empty"
    Then I fill in "path" with "."
    Then I press button "Run Import"
    Then Toast display should contain text "Imported!"

  Scenario: preview
    Given I am on "data-action/actions.php"
    When I press button "Confirm"
    Then Toast display should contain text "Confirm!"
