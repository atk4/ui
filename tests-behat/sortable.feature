Feature: Sortable / Draggable

  Scenario: drag reorder list
    Given I am on "interactive/jssortable.php"
    When I drag selector "//li[text()[normalize-space()='Argentina']]" onto selector "//li[text()[normalize-space()='Afghanistan']]"
    Then Toast display should contain text "Argentina moved from position 9 to 0"
    When I drag selector "//li[text()[normalize-space()='Bahamas']]" onto selector "//li[text()[normalize-space()='Bahamas']]"
    Then No toast should be displayed
    When I drag selector "//li[text()[normalize-space()='Bahamas']]" onto selector "//li[text()[normalize-space()='Argentina']]"
    Then Toast display should contain text "Bahamas moved from position 15 to 0"

  Scenario: drag reorder grid
    When I drag selector "//tr[td[2][text()='Albania']]/td[1]/i" onto selector "//tr[td[2][text()='Andorra']]/td[1]/i"
    Then Toast display should contain text "New order: 1 - 3 - 4 - 5 - 2 - 6"

  Scenario: drag column resize
    Given I am on "collection/table2.php"
    When I drag selector "(//div.grip-resizable)[2]" onto selector "(//div.grip-resizable)[1]"
    Then Toast display should contain text 'New widths: { "action": "wide", "amount": "narrow", "amount_copy": "wide" }'
