Feature: VirtualPage

  Scenario:
    Given I am on "interactive/virtual.php"
    Then I click link 'More info on Car'
    Then I check if text in ".__atk-behat-test-car" match text "Car"
    Then I press button "Open Lorem Ipsum"
    Then Modal is open with text 'This is yet another modal'

  Scenario:
    Given I am on "interactive/virtual.php"
    Then I press button 'Load in Modal'
    Then Modal is open with text 'Contents of your pop-up here'
    Then I click close modal

  Scenario:
    Then I click link 'Inside current layout'
    Then I check if text in ".__atk-behat-test-content" match text "Contents of your pop-up here"

  Scenario:
    Given I am on "interactive/virtual.php"
    Then I click link 'On a blank page'
    Then I check if text in ".__atk-behat-test-content" match text "Contents of your pop-up here"

  Scenario:
    Given I am on "_unit-test/virtual-page.php"
    Then I click link 'Open First'
    Then I check if text in ".__atk-behat-test-first" match text "First Level Page"
    Then I click link 'Open Second'
    Then I check if text in ".__atk-behat-test-second" match text "Second Level Page"
    Then I click link 'Open Third'
    Then I check if text in ".__atk-behat-test-third" match text "Third Level Page"
    Then I select value "Beverages" in lookup "category"
    Then I press button "Save"
    Then Toast display should contain text 'Beverages'
