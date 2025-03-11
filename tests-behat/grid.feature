Feature: Grid

  Scenario: search
    Given I am on "collection/grid.php"
    When I search grid for "kingdom"
    Then I should see "United Kingdom"
    When I press button "Test"
    Then Toast display should contain text "United Kingdom"
    When I click using selector "i.atk-remove-icon"
    Then I should not see "United Kingdom"
    When I search grid for "kingdom"
    Then I should see "United Kingdom"
    When I write "[escape]" into selector "input.atk-grid-search"
    Then I should not see "United Kingdom"

  Scenario: search no ajax
    Given I am on "collection/grid.php?no-ajax=1"
    When I search grid for "kingdom"
    Then PATCH MINK the URL should match "~_q=kingdom~"
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
    When I hide js modal
    When I click using selector "//div[@id='grid']//tr[2]//div.ui.dropdown[div[text()='Actions...']]"
    Then No toast should be displayed
    When I click using selector "//div[@id='grid']//tr[2]//div.ui.dropdown[div[text()='Actions...']]//div.menu/div[text()='Action MenuItem']"
    Then Toast display should contain text "Clicked Action MenuItem"
    Then PATCH MINK the URL should match "~_unit-test/grid-rowclick.php$~"
    When I click using selector "//div[@id='grid']//tr[2]//a"
    Then No toast should be displayed
    Then PATCH MINK the URL should match "~_unit-test/grid-rowclick.php#test~"

  Scenario: master checkbox
    Given I am on "_unit-test/grid-master-checkbox.php"
    Then Element "//div.ui.master.checkbox" should not contain class "checked"
    Then Element "//div.ui.master.checkbox" should not contain class "indeterminate"
    Then Element "//div.ui.menu/div.item[text()='Show selected']" should contain class "disabled"
    When I click using selector "//tr[1]//div.ui.child.checkbox"
    Then Element "//div.ui.master.checkbox" should not contain class "checked"
    Then Element "//div.ui.master.checkbox" should contain class "indeterminate"
    Then Element "//div.ui.menu/div.item[text()='Show selected']" should not contain class "disabled"
    When I press button "Show selected"
    Then Toast display should contain text "Selected: 1#"
    When I click using selector "//div.ui.master.checkbox"
    Then Element "//div.ui.master.checkbox" should contain class "checked"
    Then Element "//div.ui.master.checkbox" should not contain class "indeterminate"
    Then Element "//div.ui.menu/div.item[text()='Show selected']" should not contain class "disabled"
    When I press button "Show selected"
    Then Toast display should contain text "Selected: 1, 2, 3, 4, 5#"
    When I click using selector "//div.ui.master.checkbox"
    Then Element "//div.ui.master.checkbox" should not contain class "checked"
    Then Element "//div.ui.master.checkbox" should not contain class "indeterminate"
    Then Element "//div.ui.menu/div.item[text()='Show selected']" should contain class "disabled"
    When I click paginator page "2"
    When I click using selector "//tr[2]//div.ui.child.checkbox"
    When I click using selector "//tr[4]//div.ui.child.checkbox"
    Then Element "//div.ui.master.checkbox" should not contain class "checked"
    Then Element "//div.ui.master.checkbox" should contain class "indeterminate"
    Then Element "//div.ui.menu/div.item[text()='Show selected']" should not contain class "disabled"
    When I press button "Show selected"
    Then Toast display should contain text "Selected: 7, 9#"

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

  Scenario: Row remote action - load record
    Given I am on "collection/grid.php"
    Then I should not see "Bahamas"
    When I click paginator page "2"
    Then I should see "Bahamas"
    When I click using selector "//tr[td[text()='Bahamas']]//div.ui.button[text()='Say HI']"
    Then Toast display should contain text "Loaded \"Bahamas\" from ID=16"

  Scenario: Row remote action - change row CSS
    Given I am on "interactive/scroll-grid-container.php"
    When I click using selector "//tr[td[text()='Algeria']]//div.ui.button[text()='red']"
    Then Element "//tr[td[text()='Algeria'] and .//div.ui.button[text()='red']]" attribute "style" should contain text "color: red;"

  Scenario: Bulk action
    Given I am on "collection/grid.php"
    Then Element "//div.ui.menu/div.item[text()='Show selected']" should contain class "disabled"
    When I click using selector "//tr[5]//div.ui.checkbox"
    When I click using selector "//tr[8]//div.ui.checkbox"
    Then Element "//div.ui.menu/div.item[text()='Show selected']" should not contain class "disabled"
    When I press button "Show selected"
    Then Toast display should contain text "Selected: 5, 8#"

  Scenario: Bulk modal action
    Given I am on "collection/grid.php"
    Then Element "//div.ui.menu/div.item[text()='Delete selected']" should contain class "disabled"
    When I click using selector "//tr[5]//div.ui.checkbox"
    When I click using selector "//tr[8]//div.ui.checkbox"
    Then Element "//div.ui.menu/div.item[text()='Delete selected']" should not contain class "disabled"
    When I press button "Delete selected"
    Then Modal is open with text "The selected records will be permanently deleted: 5, 8#"
    When I press button "Delete"
    Then I should see "Success"
