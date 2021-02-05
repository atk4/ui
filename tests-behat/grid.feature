Feature: Grid
  Testing grid search

  Scenario: search
    Given I am on "collection/grid.php"
    Then I search grid for "kingdom"
#    make sure auto query trigger
    And I wait for loading to start in "button.atk-search-button"
    Then I should see "United Kingdom"
    Then I press button "Test"
    Then Toast display should contains text "United Kingdom"
#    click search remove icon
    Then I click icon using css "i.atk-remove-icon"
    Then I should not see "United Kingdom"

  Scenario: search no ajax
    Given I am on "collection/grid.php?no-ajax=1"
    Then I search grid for "kingdom"
    And I wait for loading to start in "button.atk-search-button"
    ## Firefox needs some waiting time.
    Then I wait 50 ms
    Then I should see "United Kingdom"
    Then page url should contains '_q=kingdom'
