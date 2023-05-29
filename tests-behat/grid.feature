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
    When I click using selector "//div[@id='grid']//tr[2]//td[2]"
    Then Toast display should contain text "Clicked on row"
    When I click using selector "//div[@id='grid']//tr[2]//div.ui.checkbox"
    Then No toast should be displayed
    When I click using selector "//div[@id='grid']//tr[2]//div.ui.button[text()='Action Button']"
    Then Toast display should contain text "Clicked Action Button"
    When I click using selector "//div[@id='grid']//tr[2]//div.ui.button[text()='Action Modal']"
    Then No toast should be displayed
    Then I should see "Clicked Action Modal: Albania"
    Then I hide js modal
    When I click using selector "//div[@id='grid']//tr[2]//div.ui.dropdown[div[text()='Actions...']]"
    Then No toast should be displayed
    When I click using selector "//div[@id='grid']//tr[2]//div.ui.dropdown[div[text()='Actions...']]//div.menu/div[text()='Action MenuItem']"
    Then Toast display should contain text "Clicked Action MenuItem"
    Then PATCH MINK the url should match "~_unit-test/grid-rowclick.php$~"
    When I click using selector "//div[@id='grid']//tr[2]//a"
    Then No toast should be displayed
    Then PATCH MINK the url should match "~_unit-test/grid-rowclick.php#test~"

  Scenario: master checkbox
    Given I am on "_unit-test/grid-rowclick.php"
    When I press menu button "Show Selection" using selector ".ui.menu.atk-grid-menu"
    Then Toast display should contain text "Selected: #"
    When I click using selector "xpath(//div[@id='grid']//tr[1]//div.ui.child.checkbox)"
    Then I press menu button "Show Selection" using selector ".ui.menu.atk-grid-menu"
    Then Toast display should contain text "Selected: 1#"
    When I click using selector "xpath(//div[@id='grid']//tr//div.ui.master.checkbox)"
    Then I press menu button "Show Selection" using selector ".ui.menu.atk-grid-menu"
    Then Toast display should contain text "Selected: 1,2,3,4,5#"
    When I click using selector "xpath(//div[@id='grid']//tr//div.ui.master.checkbox)"
    Then I press menu button "Show Selection" using selector ".ui.menu.atk-grid-menu"
    Then Toast display should contain text "Selected: #"

  Scenario: popup column header
    Given I am on "collection/tablecolumnmenu.php"
    Then I should not see "Name popup"
    When I click using selector "(//th//div.atk-table-dropdown)[1]/i"
    Then I should see "Name popup"
    Then I should not see "This popup is loaded dynamically"
    When I click using selector "(//th//div.atk-table-dropdown)[2]/i"
    Then I should see "This popup is loaded dynamically"
    When I click using selector "(//th//div.atk-table-dropdown)[3]/div.dropdown"
    When I click using selector "(//th//div.atk-table-dropdown)[3]/div.dropdown/div.menu/div.item[2]"
    Then Toast display should contain text "Title item: Reorder"

  Scenario: sort
    Given I am on "collection/grid.php"
    When I click using selector "//th.sortable[//div[text()='Name']]"
    Then I should see "Andorra"
    Then I should not see "Zambia"
    When I click using selector "//th.sortable[//div[text()='Name']]"
    Then I should see "Zambia"
    Then I should not see "Andorra"
    When I click using selector "//th.sortable[//div[text()='Name']]"
    When I click using selector "//th.sortable[//div[text()='Name']]"
    Then I should see "Andorra"
    Then I should not see "Zambia"
