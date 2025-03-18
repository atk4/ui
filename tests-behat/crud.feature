Feature: Crud

  Scenario:
    Given I am on "_unit-test/crud.php"

  Scenario: add
    When I press button "Add Country"
    When I fill in "atk_fp_country__name" with "Test"
    When I fill in "atk_fp_country__iso" with "XT"
    When I fill in "atk_fp_country__iso3" with "XTT"
    When I fill in "atk_fp_country__numcode" with "123"
    When I fill in "atk_fp_country__phonecode" with "1"
    When I press Modal button "Save"
    Then Toast display should contain text "Country action \"add\" with \"Test\" entity was executed."

  Scenario: search
    When I search grid for "united kingdom"
    Then I should see "United Kingdom"
    Then I should not see "No records"

  Scenario: add after search and sort
    # cover https://github.com/atk4/ui/commit/d42b07fbcc
    # TODO generalize JS reload with component reload
    When I click using selector "//th.sortable[//div[text()='Name']]"
    Then I should see "United Kingdom"
    When I press button "Add Country"
    When I fill in "atk_fp_country__name" with "Test 2"
    When I fill in "atk_fp_country__iso" with "XT"
    When I fill in "atk_fp_country__iso3" with "XTT"
    When I fill in "atk_fp_country__numcode" with "123"
    When I fill in "atk_fp_country__phonecode" with "1"
    When I press Modal button "Save"
    Then Toast display should contain text "Country action \"add\" with \"Test 2\" entity was executed."
    # TODO add should keep search
    # related with https://github.com/atk4/ui/issues/526 (list newly added record first)
    When I search grid for "united kingdo"

  Scenario: edit
    When I press button "Edit"
    Then Modal is open with text "Edit Country"
    When I press Modal button "Save"
    Then Toast display should contain text "Country action \"edit\" with \"United Kingdom\" entity was executed."
    # make sure search query stick
    Then I should see "United Kingdom"

  Scenario: edit - with unlocked DB
    Given I am on "_unit-test/crud.php"
    When I search grid for "united kingdom"

    Then I should not see "My United Kingdom"
    When I persist DB changes across requests
    When I press button "Edit"
    Then Modal is open with text "Edit Country"
    When I fill in "atk_fp_country__name" with "My United Kingdom"
    When I press Modal button "Save"
    Then Toast display should contain text "Record has been saved!"
    Then I should see "My United Kingdom"

  Scenario: delete
    When I press button "Delete"
    When I press Modal button "Ok"
    Then Toast display should contain text "Country action \"delete\" with \"United Kingdom\" entity was executed."
    Then I should not see "United Kingdom"

  Scenario: search across multiple columns
    When I search grid for "420 zech"
    Then I should see "Czech Republic"

  Scenario: search no match
    When I search grid for "420X zech"
    Then I should see "No records"
    Then I should not see "Czech Republic"

  Scenario: Modal in modal
    Given I am on "_unit-test/crud-nested.php"

    When I click using selector "(//div.ui.button[i.icon.book])[1]"
    Then Modal is open with text "Edit product category"
    When I click using selector "(//div.modal.active//div.ui.button[i.icon.edit])[1]"
    Then Modal is open with text "Edit Product"
    Then input "atk_fp_product__name" value should start with "Mustard"
    When I press Modal button "Save"
    When I click close modal

    When I click using selector "(//div.ui.button[i.icon.book])[1]"
    Then Modal is open with text "Edit product category"
    When I click using selector "(//div.modal.active//div.ui.button[i.icon.edit])[2]"
    Then Modal is open with text "Edit Product"
    Then input "atk_fp_product__name" value should start with "Ketchup"
    When I press Modal button "Save"
    When I click close modal

    When I click using selector "(//div.ui.button[i.icon.book])[2]"
    Then Modal is open with text "Edit product category"
    When I click using selector "(//div.modal.active//div.ui.button[i.icon.edit])[1]"
    Then Modal is open with text "Edit Product"
    Then input "atk_fp_product__name" value should start with "Cola"
    When I press Modal button "Save"
    When I click close modal

  Scenario: edit /w array persistence (strict comparison)
    Given I am on "collection/crud3.php"
    When I click using selector "//table//tr[3]//i.icon.edit"
    Then Modal is open with text "Edit Country"
    When I press Modal button "Save"
    Then Toast display should contain text "Record has been saved!"

  Scenario: delete /w array persistence (strict comparison)
    When I click using selector "//table//tr[3]//i.icon.trash"
    Then Toast display should contain text "Record has been deleted!"
