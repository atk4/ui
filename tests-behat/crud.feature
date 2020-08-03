Feature: Crud
  Testing crud add, edit, delete using search

  Scenario:
    Given I am on "_unit-test/crud.php"

  Scenario: add
    Then I press menu button "Add" using class "atk-grid-menu"
    And wait for callback
    And I sleep 50 ms
    Then I fill in "name" with "Test"
    Then I fill in "iso" with "TT"
    Then I fill in "iso3" with "TTT"
    Then I fill in "numcode" with "123"
    Then I fill in "phonecode" with "1"
    Then I press button "AddMe"
    And wait for callback
    Then Toast display should contains text "Form Submit"

  Scenario: search
    Then I search grid for "united kingdom"
#    make sure auto query trigger
    And I wait for loading to start in "button.atk-search-button"
    And wait for callback
    Then I should see "United Kingdom"

  Scenario: edit
    Then I press button "Edit"
    And wait for callback
    Then Modal is open with text "Edit Country"
    Then I press button "EditMe"
    And wait for callback
    Then Toast display should contains text "Form Submit"
    And wait for callback
#    make sure search query stick
    Then I should see "United Kingdom"

  Scenario: delete
    Then I press button "Delete"
    And I sleep 50 ms
    Then I press button "Ok"
    And wait for callback
    Then I should not see "United Kingdom"
