Feature: Jquery

  Scenario:
    Given I am on "_unit-test/jquery-data.php"
    When I press button "Display type"
    Then Toast display should contain text "Type: string, string"
    When I press button "Call $elem.data('foo', 5)"
    When I press button "Display type"
    Then Toast display should contain text "Type: number, number"
    When I press button "Call $elem.removeData()"
    When I press button "Display type"
    Then Toast display should contain text "Type: string, string"
    When I press button "Call $elem.data('foo', 5)"
    When I press button "Display type"
    Then Toast display should contain text "Type: number, number"
    When I press button "Call $elem.removeData('foo')"
    When I press button "Display type"
    Then Toast display should contain text "Type: string, string"
