Feature: VirtualPage

  Scenario:
    Given I am on "interactive/virtual.php"
    Then I click link 'More info on Car'
    Then text in container using '.__atk-behat-test-car' should contains 'Car'

  Scenario:
    Given I am on "interactive/virtual.php"
    Then I press button 'Load in Modal'
    Then Modal is open with text 'Contents of your pop-up here'

  Scenario:
    Given I am on "interactive/virtual.php"
    Then I click link 'Inside current layout'
    Then text in container using '.__atk-behat-test-content' should contains 'Contents of your pop-up here'

  Scenario:
    Given I am on "_unit-test/virtual-page.php"
    Then I click link 'Open First'
    Then text in container using '.__atk-behat-test-first' should contains 'First Level Page'
    Then I click link 'Open Second'
    Then text in container using '.__atk-behat-test-second' should contains 'Second Level Page'
    Then I click link 'Open Third'
    Then text in container using '.__atk-behat-test-third' should contains 'Third Level Page'
    Then I select value "Beverages" in lookup "category"
    Then I press button "Save"
    Then Toast display should contains text 'Beverages'
