Feature: Crud
  Testing crud add, edit, delete using search

  Scenario:
    Given I am on "_unit-test/crud.php"

  Scenario: add
    Then I press menu button "Add Country" using class "atk-grid-menu"
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
