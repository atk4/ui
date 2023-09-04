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

  Scenario: IPP selector
    Then I should see "Andorra"
    Then I should not see "China"
    Then I should not see "Zambia"
    When I click using selector "//div.ui.dropdown.compact"
    When I click using selector "//div.ui.dropdown.compact//div.item[text()='100']"
    Then I should see "Andorra"
    Then I should see "China"
    Then I should not see "Zambia"
    When I click using selector "//div.ui.dropdown.compact"
    When I click using selector "//div.ui.dropdown.compact//div.item[text()[normalize-space()='1 000']]"
    Then I should see "Andorra"
    Then I should see "China"
    Then I should see "Zambia"

  Scenario: Bulk action
    Given I am on "collection/grid.php"
    Then I press button "Show selected"
    Then Toast display should contain text "Selected: #"
    When I click using selector "//tr[5]//div.ui.checkbox"
    When I click using selector "//tr[8]//div.ui.checkbox"
    Then I press button "Show selected"
    Then Toast display should contain text "Selected: 5, 8#"

  Scenario: Bulk modal action
    Given I am on "collection/grid.php"
    Then I press button "Delete selected"
    Then Modal is open with text "The selected records will be permanently deleted: #"
    Then I press button "Delete"
    Then I should see "Success"
    Then I click close modal
    When I click using selector "//tr[5]//div.ui.checkbox"
    When I click using selector "//tr[8]//div.ui.checkbox"
    Then I press button "Delete selected"
    Then Modal is open with text "The selected records will be permanently deleted: 5, 8#"
    Then I press button "Delete"
    Then I should see "Success"
