Feature: SSE

  Scenario:
    Given I am on "interactive/sse.php"
    When I press button "Turn On"
    When I press button "Turn Off"
    # TODO test SSE result compatible with Slow Chrome CI job
    # Then I should see "20%"
