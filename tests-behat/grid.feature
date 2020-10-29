Feature: Grid
  Testing grid search

  Scenario: search
    Given I am on "collection/grid.php"
    Then I search grid for "kingdom"
#    make sure auto query trigger
    Then I should see "United Kingdom"
    Then I press button "Test"
    Then Toast display should contains text "United Kingdom"
#    click search remove icon
    Then I click icon using css "i.atk-remove-icon"
    Then I should not see "United Kingdom"
