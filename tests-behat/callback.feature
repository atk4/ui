Feature: Callback

  Scenario:
    Given I am on "_unit-test/callback.php"
    When I press button "First"
    Then I should see "TestName"
    When I press Modal button "Save"
    Then Toast display should contain text "Save"
    Then I should not see "TestName"

  Scenario:
    Given I am on "_unit-test/callback-nested.php"
    When I press button "Load1"
    Then I should see "Loader-1"
    When I press button "Load2"
    Then I should see "Loader-2"
    Then I should see "Loader-3"
    When I click paginator page "2"
    When I click using selector "(//div.ui.atk-test.button)[1]"
    Then Modal is open with text "Edit Country"
    When I press Modal button "Save"
    Then Toast display should contain text "Country action \"edit\" with \"Andorra\" entity was executed."
