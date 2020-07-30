Feature: Callback
  Testing callbacks

  Scenario:
    Given I am on "_unit-test/callback_1.php"
    And I press button "Edit First"
    And wait for callback
    Then I sleep 500 ms
    Then I should see "TestName"
    And I press button "Save"
    And wait for callback
    Then Toast display should contains text "Save"
    Then I sleep 500 ms
    Then I should not see "TestName"
