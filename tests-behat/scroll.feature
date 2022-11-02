Feature: Dynamic scroll

  Scenario:
    Given I am on "interactive/scroll-lister.php"
    Then I should see "Argentina"
    Then I should not see "Denmark"
    When I scroll to bottom
    Then I should see "Denmark"
    When I scroll to bottom
    When I scroll to bottom
    When I scroll to bottom
    When I scroll to bottom
    When I scroll to bottom
    When I scroll to bottom
    Then I should not see "South Sudan"
    When I scroll to bottom
    Then I should see "South Sudan"
    When I scroll to bottom
    Then I should see "South Sudan"
    Then I should see "Denmark"

  Scenario: with row events
    Given I am on "_unit-test/scroll.php"
    Then I should see "Argentina"
    Then I should not see "Canada"
    When I click using selector "xpath(//tr[td[text()='Austria']]//td[2])"
    Then Toast display should contain text "row clicked: 14"
    When I click using selector "xpath(//tr[td[text()='Austria']]//i.icon.bell)"
    Then Toast display should contain text "action clicked: 14"
    Then I should see "Argentina"
    Then I should not see "Canada"
    When I scroll to bottom
    Then I should see "Canada"
    When I click using selector "xpath(//tr[td[text()='Canada']]//td[2])"
    Then Toast display should contain text "row clicked: 38"
    When I click using selector "xpath(//tr[td[text()='Canada']]//i.icon.bell)"
    Then Toast display should contain text "action clicked: 38"
    When I scroll to top
    When I click using selector "xpath(//tr[td[text()='Austria']]//td[2])"
    Then Toast display should contain text "row clicked: 14"
    When I click using selector "xpath(//tr[td[text()='Austria']]//i.icon.bell)"
    Then Toast display should contain text "action clicked: 14"
