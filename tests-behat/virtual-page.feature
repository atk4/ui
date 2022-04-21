Feature: VirtualPage

  Scenario:
    Given I am on "interactive/virtual.php"
    Then I click link 'More info on Car'
    Then text in container using selector ".__atk-behat-test-car" should contain 'Car'
    Then I press button "Open Lorem Ipsum"
    Then Modal is open with text 'This is yet another modal'

  Scenario:
    Given I am on "interactive/virtual.php"
    Then I press button 'Load in Modal'
    Then Modal is open with text 'Contents of your pop-up here'

  Scenario:
    Given I am on "interactive/virtual.php"
    Then I click link 'Inside current layout'
    Then text in container using selector ".__atk-behat-test-content" should contain 'Contents of your pop-up here'

  Scenario:
    Given I am on "_unit-test/virtual-page.php"
    Then I click link 'Open First'
    Then text in container using selector ".__atk-behat-test-first" should contain 'First Level Page'
    Then I click link 'Open Second'
    Then text in container using selector ".__atk-behat-test-second" should contain 'Second Level Page'
    Then I click link 'Open Third'
    Then text in container using selector ".__atk-behat-test-third" should contain 'Third Level Page'
    Then I select value "Beverages" in lookup "category"
    Then I press button "Save"
    Then Toast display should contain text 'Beverages'
