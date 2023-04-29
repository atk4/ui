Feature: Tab

  Scenario:
    Given I am on "interactive/tabs.php"
    Then Active tab should be "Default Active Tab"
    Then I should see "This is the active tab by default"
    Then I click tab with title "Dynamic Lorem Ipsum"
    Then I should see "you will see a different text"
