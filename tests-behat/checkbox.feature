Feature: Checkbox

  Scenario:
    Given I am on "form-control/checkbox.php"
    When I press button "Save"
    Then Toast display should contain text '{ "test": false, "test_checked": true, "also_checked": true }'

    When I click using selector "//div.ui.checkbox[not(self::*.checked)][input[@name='test']]"
    When I click using selector "//div.ui.checkbox.checked[input[@name='test_checked']]"
    When I click using selector "//div.ui.checkbox.checked[input[@name='also_checked']]"
    When I press button "Save"
    Then Toast display should contain text '{ "test": true, "test_checked": false, "also_checked": false }'
