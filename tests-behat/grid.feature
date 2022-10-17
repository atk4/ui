Feature: Grid

  Scenario: search
    Given I am on "collection/grid.php"
    Then I search grid for "kingdom"
    Then I should see "United Kingdom"
    Then I press button "Test"
    Then Toast display should contain text "United Kingdom"
    # click search remove icon
    Then I click using selector "i.atk-remove-icon"
    Then I should not see "United Kingdom"

  Scenario: search no ajax
    Given I am on "collection/grid.php?no-ajax=1"
    Then I search grid for "kingdom"
    Then PATCH MINK the url should match "~_q=kingdom~"
    Then I should see "United Kingdom"

  Scenario: Checkbox click event must not bubble to row click
    Given I am on "_unit-test/grid-rowclick.php"
    When I click using selector "xpath(//div[@id='grid']//tr[2]//td[2])"
    Then Toast display should contain text "Clicked on row"
    When I click using selector "xpath(//div[@id='grid']//tr[2]//div.ui.checkbox)"
    Then No toast should be displayed
    When I click using selector "xpath(//div[@id='grid']//tr[2]//div.ui.button[text()='Action Button'])"
    Then Toast display should contain text "Clicked Action Button"
    When I click using selector "xpath(//div[@id='grid']//tr[2]//div.ui.button[text()='Action Modal'])"
    Then No toast should be displayed
    Then I should see "Clicked Action Modal: Albania"
    Then I hide js modal
    When I click using selector "xpath(//div[@id='grid']//tr[2]//div.ui.dropdown[div[text()='Actions...']])"
    Then No toast should be displayed
    When I click using selector "xpath(//div[@id='grid']//tr[2]//div.ui.dropdown[div[text()='Actions...']]//div.menu/div[text()='Action MenuItem'])"
    Then Toast display should contain text "Clicked Action MenuItem"
    Then PATCH MINK the url should match "~_unit-test/grid-rowclick.php$~"
    When I click using selector "xpath(//div[@id='grid']//tr[2]//a)"
    Then No toast should be displayed
    Then PATCH MINK the url should match "~_unit-test/grid-rowclick.php#test~"

  Scenario: drag resize (TODO test real drag)
    Given I am on "collection/table2.php"
    Then I should see "Table with resizable columns"

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

  Scenario: dynamic scroll
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
