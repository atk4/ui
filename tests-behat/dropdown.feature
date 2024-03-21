Feature: Dropdown

  Scenario: dropdown cascade
    Given I am on "form-control/dropdown-plus.php"
    Then I select value "Beverages" in lookup "category_id"
    Then I select value "Sugar/Sweetened" in lookup "sub_category_id"
    Then I select value "Soda" in lookup "product_id"
    When I click using selector "(//div[text()='Save'])[2]"
    Then Modal is open with text '{ "category_id": "2", "sub_category_id": "9", "product_id": "4" }' in selector "p"
    Then I click close modal
    Then I should see "Soda"
    Then I select value "Coffee and Tea" in lookup "sub_category_id"
    Then I should not see "Soda"
    Then I should not see "Cola"
    Then I should not see "No results found."
    When I click using selector "//div.field[label[text()='Product ID']]//div.ui.dropdown"
    Then I should see "No results found."
