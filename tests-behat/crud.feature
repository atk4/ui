Feature: Crud
  Testing crud add, edit, delete using search

  Scenario:
    Given I am on "_unit-test/crud.php"

  Scenario: add
    Then I press menu button "Add" using class "atk-grid-menu"
    Then I fill in "name" with "Test"
    Then I fill in "iso" with "TT"
    Then I fill in "iso3" with "TTT"
    Then I fill in "numcode" with "123"
    Then I fill in "phonecode" with "1"
    Then I press button "AddMe"
    Then Toast display should contains text "Form Submit"

  Scenario: search
    Then I search grid for "united kingdom"
#    make sure auto query trigger
    Then I should see "United Kingdom"

  Scenario: edit
    Then I press button "Edit"
    Then Modal is open with text "Edit Country"
    Then I press button "EditMe"
    Then Toast display should contains text "Form Submit"
#    make sure search query stick
    Then I should see "United Kingdom"

  Scenario: delete
    Then I press button "Delete"
    Then I press button "Ok"
    Then I should not see "United Kingdom"
