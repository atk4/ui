Feature: Checkbox

  Scenario:
    Given I am on "form-control/checkbox.php"
    When I press button "Save"
    Then Toast display should contain text '{ "test": false, "test_checked": true, "also_checked": true }'
