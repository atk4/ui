Feature: Callback
  Testing callbacks

  Scenario:
    Given I am on "_unit-test/callback.php"
    Then I press button "First"
    And wait for callback
    Then I sleep 500 ms
    Then I should see "TestName"
    And I press button "Save"
    And wait for callback
    Then Toast display should contains text "Save"
    Then I sleep 500 ms
    Then I should not see "TestName"

  Scenario:
    Given I am on "_unit-test/callback_2.php"
    Then I press button "Load1"
    And wait for callback
    Then I should see "Loader-1"
    Then I press button "Load2"
    Then wait for callback
    # Loader 2 automatically trigger 3, so wait for it.
    Then wait for callback
    Then I should see "Loader-2"
    Then I should see "Loader-3"
    Then I click paginator page "2"
    And wait for callback
    Then I click first element using class ".ui.atk-test.button"
    And wait for callback
    Then Modal is open with text "Edit Country"
    Then I press button "Save"
    And wait for callback
    Then Toast display should contains text "Form Submit"
