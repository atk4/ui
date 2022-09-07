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
    Then page url should contain '_q=kingdom'
    Then I should see "United Kingdom"

  Scenario: Checkbox click event must not bubble to row click
    Given I am on "_unit-test/grid-rowclick.php#xxx"
    When I click using selector "xpath(//div[@id='grid']//tr[2]//td[2])"
    Then Toast display should contain text "Clicked on row"
    When I click using selector "xpath(//div[@id='grid']//tr[2]//div.ui.checkbox)"
    When I click using selector "xpath(//div[@id='grid']//tr[2]//a)"
    Then page url should contain '/_unit-test/grid-rowclick.php#test'
