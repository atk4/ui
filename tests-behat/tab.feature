Feature: Tab

  Scenario:
    Given I am on "interactive/tabs.php"
    Then Active tab should be "Default Active Tab"
    Then I should see "This is the active tab by default"
    When I click tab with title "Dynamic Lorem Ipsum"
    Then I should see "you will see a different text"

  Scenario: API exception is displayed
    When I click tab with title "Server exception"
    Then Modal is open with text "ErrorException: assert(): assert(false) failed"
    When I hide js modal

  Scenario: URL exception is displayed
    When I click tab with title "URL 404"
    Then Modal is open with text "API Server Error"
    Then Modal is open with text "<head><title>404 Not Found</title></head>"
    When I hide js modal
