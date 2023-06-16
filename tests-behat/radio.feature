Feature: Radio

  Scenario:
    Given I am on "form-control/form6.php"
    When I press button "Save"
    Then Toast display should contain text '"enum_d": "male", "enum_r": "male"'
    Then Toast display should contain text '"list_d": "1", "list_r": "1"'
    Then Toast display should contain text '"int_d": "7", "int_r": "7"'
    Then Toast display should contain text '"string_d": "M", "string_r": "M"'
