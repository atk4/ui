Feature: Grid

  Scenario: search
    Given I am on "collection/grid.php"
    Then I search grid for "kingdom"
    Then I should see "United Kingdom"
    Then I press button "Test"
    Then Toast display should contain text "United Kingdom"
    # click search remove icon
    Then I click using selector "i.atk-remove-icon"
    Then I should not see "United Kingdom"

  Scenario: search no ajax
    Given I am on "collection/grid.php?no-ajax=1"
    Then I search grid for "kingdom"
    Then the url should match "_q=kingdom"
    Then I should see "United Kingdom"

  Scenario: Checkbox click event must not bubble to row click
    Given I am on "_unit-test/grid-rowclick.php"
    When I click using selector "xpath(//div[@id='grid']//tr[2]//td[2])"
    Then Toast display should contain text "Clicked on row"
    When I click using selector "xpath(//div[@id='grid']//tr[2]//div.ui.checkbox)"
    Then No toast should be displayed
    When I click using selector "xpath(//div[@id='grid']//tr[2]//a)"
    Then No toast should be displayed
    Then the url should match "_unit-test/grid-rowclick.php#test"
