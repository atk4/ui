Feature: Jquery data

  Scenario: patch $elem.data()
    Given I am on "_unit-test/jquery-data.php"
    When I press button "Display types"
    Then Toast display should contain text "Types: string/string, string/string"
    When I press button "Call $elem.data(k, int)"
    When I press button "Display types"
    Then Toast display should contain text "Types: number/number, string/string"
    When I press button "Call $elem.removeData()"
    When I press button "Display types"
    Then Toast display should contain text "Types: string/string, string/string"
    When I press button "Call $elem.removeData()"
    When I press button "Call $elem.data({k: bigint})"
    When I press button "Display types"
    Then Toast display should contain text "Types: bigint/bigint, string/string"
    When I press button "Call $elem.removeData(k)"
    When I press button "Display types"
    Then Toast display should contain text "Types: undefined/string, string/string"
