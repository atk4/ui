Feature: Crud
  Testing crud add, edit, delete using search

  Scenario:
    Given I am on "_unit-test/crud.php"

  Scenario: add
    Then I press menu button "Add Country" using selector ".ui.menu.atk-grid-menu"
    Then I fill in "atk_fp_country__name" with "Test"
    Then I fill in "atk_fp_country__iso" with "TT"
    Then I fill in "atk_fp_country__iso3" with "TTT"
    Then I fill in "atk_fp_country__numcode" with "123"
    # '50ce262c' = substr(md5('phonecode'), 0, 8)
    Then I fill in "atk_fp_country__50ce262c" with "1"
    Then I press Modal button "Save"
    Then Toast display should contain text "Form Submit"

  Scenario: search
    Then I search grid for "united kingdom"
    Then I should see "United Kingdom"

  Scenario: edit
    Then I press button "Edit"
    Then Modal is open with text "Edit Country"
    Then I press Modal button "Save"
    Then Toast display should contain text "Form Submit"
    # make sure search query stick
    Then I should see "United Kingdom"

  Scenario: delete
    Then I press button "Delete"
    Then I press Modal button "Ok"
    Then I should not see "United Kingdom"

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
