Feature: Radio

  Scenario:
    Given I am on "form-control/form6.php"
    When I press button "Save"
    Then Toast display should contain text '"enum_d": "male", "enum_r": "male"'
    Then Toast display should contain text '"list_d": "1", "list_r": "1"'
    Then Toast display should contain text '"int_d": "7", "int_r": "7"'
    Then Toast display should contain text '"string_d": "M", "string_r": "M"'

    Then I select value "female" in lookup "enum_d"
    When I click using selector "//div.ui.radio[not(self::*.checked)][input[@name='enum_r'] and label[text()='female']]"
    Then I select value "female" in lookup "list_d"
    When I click using selector "//div.ui.radio[not(self::*.checked)][input[@name='list_r'] and label[text()='female']]"
    When I press button "Save"
    Then Toast display should contain text '"enum_d": "female", "enum_r": "female"'
    Then Toast display should contain text '"list_d": "0", "list_r": "0"'
    Then Toast display should contain text '"int_d": "7", "int_r": "7"'
    Then Toast display should contain text '"string_d": "M", "string_r": "M"'
    Then I select value "female" in lookup "int_d"
    When I click using selector "//div.ui.radio[not(self::*.checked)][input[@name='int_r'] and label[text()='female']]"
    Then I select value "female" in lookup "string_d"
    When I click using selector "//div.ui.radio[not(self::*.checked)][input[@name='string_r'] and label[text()='female']]"
    When I press button "Save"
    Then Toast display should contain text '"enum_d": "female", "enum_r": "female"'
    Then Toast display should contain text '"list_d": "0", "list_r": "0"'
    Then Toast display should contain text '"int_d": "5", "int_r": "5"'
    Then Toast display should contain text '"string_d": "F", "string_r": "F"'

    Then I select value "" in lookup "enum_d"
    When I click using selector "//div.ui.radio.checked[input[@name='enum_r']]"
    Then I select value "" in lookup "list_d"
    When I click using selector "//div.ui.radio.checked[input[@name='list_r']]"
    When I press button "Save"
    Then Toast display should contain text '"enum_d": null, "enum_r": null'
    Then Toast display should contain text '"list_d": null, "list_r": null'
    Then Toast display should contain text '"int_d": "5", "int_r": "5"'
    Then Toast display should contain text '"string_d": "F", "string_r": "F"'
    Then I select value "" in lookup "int_d"
    When I click using selector "//div.ui.radio.checked[input[@name='int_r']]"
    Then I select value "" in lookup "string_d"
    When I click using selector "//div.ui.radio.checked[input[@name='string_r']]"
    When I press button "Save"
    Then Toast display should contain text '"enum_d": null, "enum_r": null'
    Then Toast display should contain text '"list_d": null, "list_r": null'
    Then Toast display should contain text '"int_d": null, "int_r": null'
    Then Toast display should contain text '"string_d": null, "string_r": null'
