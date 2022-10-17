Feature: Sortable / Draggable

  Scenario: drag reorder list
    Given I am on "interactive/jssortable.php"
    When I drag selector "xpath(//li[text()[normalize-space()='Afghanistan']])" onto selector "xpath(//li[text()[normalize-space()='Argentina']])"
    Then Toast display should contain text "Afghanistan moved from position 0 to 9"
    When I drag selector "xpath(//li[text()[normalize-space()='Bahamas']])" onto selector "xpath(//li[text()[normalize-space()='Bahamas']])"
    Then No toast should be displayed
    When I drag selector "xpath(//li[text()[normalize-space()='Bahamas']])" onto selector "xpath(//li[text()[normalize-space()='Australia']])"
    Then Toast display should contain text "Bahamas moved from position 15 to 12"

  Scenario: drag reorder grid
    When I drag selector "xpath(//tr[td[2][text()='Albania']]/td[1]/i)" onto selector "xpath(//tr[td[2][text()='Andorra']]/td[1]/i)"
    Then Toast display should contain text "New order: 1 - 3 - 4 - 5 - 2 - 6"

  Scenario: drag resize (TODO test real drag)
    Given I am on "collection/table2.php"
    Then I should see "Table with resizable columns"
