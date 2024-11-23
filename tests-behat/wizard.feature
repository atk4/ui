Feature: Wizard

  Scenario: test form submit
    Given I am on "_unit-test/wizard.php"
    Then I check if text in "//div.ui.two.steps//div.ui.step.active" match text "Step 1"
    Then I should not see "Must not be empty"
    When I click using selector "//a[text()='Next']"
    Then I should see "Must not be empty"
    When I fill in "city" with "Prague"
    When I click using selector "//a[text()='Next']"
    Then I check if text in "//div.ui.two.steps//div.ui.step.active" match text "Step 2"
    Then I should not see "Must not be empty"
    When I click using selector "//a[text()='Finish']"
    Then I should see "Must not be empty"
    When I fill in "city" with "London"
    Then I should not see "Wizard completed"
    When I click using selector "//a[text()='Finish']"
    Then I should see "Wizard completed"
