Feature: Dropdown

  Scenario: dropdown cascade
    Given I am on "form-control/dropdown-plus.php"
    When I select value "Beverages" in lookup "category_id"
    When I select value "Sugar/Sweetened" in lookup "sub_category_id"
    When I select value "Soda" in lookup "product_id"
    When I click using selector "(//div[text()='Save'])[2]"
    Then Modal is open with text '{ "category_id": "2", "sub_category_id": "9", "product_id": "4" }' in selector "p"
    When I click close modal
    Then I should see "Soda"
    When I select value "Coffee and Tea" in lookup "sub_category_id"
    Then I should not see "Soda"
    Then I should not see "Cola"
    Then I should not see "No results found."
    When I click using selector "//div.field[label[text()='Product']]//div.ui.dropdown"
    Then I should see "No results found."

  Scenario: dropdown multiple
    Given I am on "form-control/dropdown-plus.php"
    Then I check if input value for "input[name='multi']" match text ""
    When I select value "Option 2" in lookup "multi"
    When I select value "Option 1" in lookup "multi"
    Then I check if input value for "input[name='multi']" match text "option2,option1"
    When I select value "" in lookup "multi"
    Then I check if input value for "input[name='multi']" match text ""

  Scenario: dropdown with escaped HTML
    Given I am on "_unit-test/dropdown-html.php"
    When I press button "Save"
    Then Modal is open with text "match init: 1"
    When I click close modal
    When I select value "uTitle <b>\"' &lt;" in lookup "dropdown_single"
    When I select value "uTitle <b>\"' &lt;" in lookup "dropdown_single2"
    When I select value "uTitle <b>\"' &lt;" in lookup "dropdown_multi"
    When I select value "uTitle <b>\"' &lt;" in lookup "dropdown_multi2"
    When I press button "Save"
    Then Modal is open with text "match u add: 1"
    When I click close modal
    When I select value "" in lookup "dropdown_single"
    When I select value "" in lookup "dropdown_single2"
    When I select value "" in lookup "dropdown_multi"
    When I select value "" in lookup "dropdown_multi2"
    When I press button "Save"
    Then Modal is open with text "match empty: 1"
    When I click close modal
    When I select value "uTitle <b>\"' &lt;" in lookup "dropdown_single"
    When I select value "uTitle <b>\"' &lt;" in lookup "dropdown_single2"
    When I select value "uTitle <b>\"' &lt;" in lookup "dropdown_multi"
    When I select value "uTitle <b>\"' &lt;" in lookup "dropdown_multi2"
    When I press button "Save"
    Then Modal is open with text "match u only: 1"

  Scenario: dropdown menu
    Given I am on "basic/menu.php"
    When I click using selector "//div.ui.dropdown[div[text()='With Callback']]"
    When I click using selector "//div.ui.dropdown[div[text()='With Callback']]//div.item[text()='c']"
    Then Toast display should contain text "New selected item: c"

  Scenario: dropdown menu from model
    When I click using selector "//div.ui.dropdown[div[text()='From Model']]"
    When I click using selector "//div.ui.dropdown[div[text()='From Model']]//div.item[text()='Beverages']"
    Then Toast display should contain text "New selected item: Beverages"
