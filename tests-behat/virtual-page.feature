Feature: VirtualPage

  Scenario:
    Given I am on "interactive/virtual.php"
    When I click link "More info on Car"
    Then I check if text in ".__atk-behat-test-car" match text "Car"
    When I press button "Open Lorem Ipsum"
    Then Modal is open with text "This is yet another modal"

  Scenario:
    Given I am on "interactive/virtual.php"
    When I press button "Load in Modal"
    Then Modal is open with text "Contents of your pop-up here"
    When I click close modal

  Scenario:
    When I click link "Inside current layout"
    Then I check if text in ".__atk-behat-test-content" match text "Contents of your pop-up here"

  Scenario:
    Given I am on "interactive/virtual.php"
    When I click link "On a blank page"
    Then I check if text in ".__atk-behat-test-content" match text "Contents of your pop-up here"

  Scenario:
    Given I am on "_unit-test/virtual-page.php"
    When I click link "Open First"
    Then I check if text in ".__atk-behat-test-first" match text "First Level Page"
    When I click link "Open Second"
    Then I check if text in ".__atk-behat-test-second" match text "Second Level Page"
    When I click link "Open Third"
    Then I check if text in ".__atk-behat-test-third" match text "Third Level Page"
    When I select "Beverages" in lookup "category"
    When I press button "Save"
    Then Toast display should contain text "Beverages"
