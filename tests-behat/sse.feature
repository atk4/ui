Feature: SSE

  Scenario:
    Given I am on "interactive/sse.php"
    When I press button "Turn On"
    When I press button "Turn Off"
    # TODO test SSE result compatible with Slow Chrome CI job
    # Then I should see "20%"

  Scenario:
    Then I should not see "This is my new text!"
    When I press button "Click me to change my text"
    When I press Modal button "Ok"
    # TODO wait until SSE is complete
    # Then I should see "This is my new text!"
