Feature: Late error handling

  Scenario:
    Given I am on "_unit-test/late-output-error.php"
    When I press button "Test LateOutputError I: Headers already sent"
    Then Modal is open with text "API Server Error"
    Then Modal is open with text "!! FATAL UI ERROR: Headers already sent, more headers cannot be set at this stage !!"
    Then I hide js modal

  Scenario:
    When I press button "Test LateOutputError II: Unexpected output detected"
    Then Modal is open with text "API Server Error"
    Then Modal is open with text "unmanaged output !! FATAL UI ERROR: Unexpected output detected !!"
    Then I hide js modal

  Scenario:
    When I press button "Test LateOutputError III: Unexpected output detected"
    Then Modal is open with text "API Server Error"
    Then Modal is open with text "unmanaged output !! FATAL UI ERROR: Unexpected output detected !!"
    Then I hide js modal
