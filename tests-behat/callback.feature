Feature: Callback
  Testing callbacks

  Scenario:
    Given I am on "_unit-test/callback.php"
    Then I press button "First"
    Then I should see "TestName"
    And I press button "Save"
    Then Toast display should contains text "Save"
    Then I should not see "TestName"

  Scenario:
    Given I am on "_unit-test/callback_2.php"
    Then I press button "Load1"
    Then I should see "Loader-1"
    Then I press button "Load2"
    Then I should see "Loader-2"
    Then I should see "Loader-3"
    Then I click paginator page "2"
    Then I click first element using class ".ui.atk-test.button"
    Then Modal is open with text "Edit Country"
    Then I press button "Save"
    Then Toast display should contains text "Form Submit"
