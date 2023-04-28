Feature: Crud

  Scenario:
    Given I am on "_unit-test/crud.php"

  Scenario: add
    Then I press menu button "Add Country" using selector ".ui.menu.atk-grid-menu"
    Then I fill in "atk_fp_country__name" with "Test"
    Then I fill in "atk_fp_country__iso" with "TT"
    Then I fill in "atk_fp_country__iso3" with "TTT"
    Then I fill in "atk_fp_country__numcode" with "123"
    Then I fill in "atk_fp_country__phonecode" with "1"
    Then I press Modal button "Save"
    Then Toast display should contain text 'Country action "add" with "Test" entity was executed.'

  Scenario: search
    Then I search grid for "united kingdom"
    Then I should see "United Kingdom"
    Then I should not see "No records"

  Scenario: edit
    Then I press button "Edit"
    Then Modal is open with text "Edit Country"
    Then I press Modal button "Save"
    Then Toast display should contain text 'Country action "edit" with "United Kingdom" entity was executed.'
    # make sure search query stick
    Then I should see "United Kingdom"

  Scenario: edit - with cross-request DB modifications
    Then I should not see "My United Kingdom"
    When I allow cross-request DB modifications
    Then I press button "Edit"
    Then Modal is open with text "Edit Country"
    Then I fill in "atk_fp_country__name" with "My United Kingdom"
    Then I press Modal button "Save"
    Then Toast display should contain text 'Country action "edit" with "United Kingdom" entity was executed.'
    Then I should see "My United Kingdom"

  Scenario: delete
    Then I press button "Delete"
    Then I press Modal button "Ok"
    Then Toast display should contain text 'Country action "delete" with "United Kingdom" entity was executed.'
    Then I should not see "United Kingdom"

  Scenario: search across multiple columns
    Then I search grid for "420 zech"
    Then I should see "Czech Republic"

  Scenario: search no match
    Then I search grid for "420X zech"
    Then I should see "No records"
    Then I should not see "Czech Republic"

  Scenario: Modal in modal
    Given I am on "_unit-test/crud-nested.php"

    Then I click using selector "xpath((//div.ui.button[i.icon.book])[1])"
    Then Modal is open with text "Edit product category"
    Then I click using selector "xpath((//div.modal.active//div.ui.button[i.icon.edit])[1])"
    Then Modal is open with text "Edit Product"
    Then input "atk_fp_product__name" value should start with "Mustard"
    When I press Modal button "Save"
    Then I click close modal

    Then I click using selector "xpath((//div.ui.button[i.icon.book])[1])"
    Then Modal is open with text "Edit product category"
    Then I click using selector "xpath((//div.modal.active//div.ui.button[i.icon.edit])[2])"
    Then Modal is open with text "Edit Product"
    Then input "atk_fp_product__name" value should start with "Ketchup"
    When I press Modal button "Save"
    Then I click close modal

    Then I click using selector "xpath((//div.ui.button[i.icon.book])[2])"
    Then Modal is open with text "Edit product category"
    Then I click using selector "xpath((//div.modal.active//div.ui.button[i.icon.edit])[1])"
    Then Modal is open with text "Edit Product"
    Then input "atk_fp_product__name" value should start with "Cola"
    When I press Modal button "Save"
    Then I click close modal

  Scenario: edit /w array persistence (strict comparison)
    Given I am on "collection/crud3.php"
    Then I click using selector "xpath(//table//tr[3]//i.icon.edit)"
    Then Modal is open with text "Edit Country"
    Then I press Modal button "Save"
    Then Toast display should contain text "Record has been saved!"

  Scenario: delete /w array persistence (strict comparison)
    Then I click using selector "xpath(//table//tr[3]//i.icon.trash)"
    Then Toast display should contain text "Record has been deleted!"
