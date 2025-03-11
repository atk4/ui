Feature: Remove observer

  Scenario:
    Given I am on "_unit-test/remove-observer.php"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0"
    When I press button "Reload V"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0 V1"
    When I press button "Reload V"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0 V1 V2"
    When I press button "Reload I"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0 V1 V2 I3 U3 V3"
    When I fill field using "#log" with ""
    When I press button "Add A handler"
    When I press button "Add U handler"
    When I press button "Reload V"
    Then I check if input value for "#log" match text "V4"
    When I press button "Add V handler"
    When I press button "Reload V"
    Then I check if input value for "#log" match text "V4 hV4 V5"
    When I press button "Reload V"
    Then I check if input value for "#log" match text "V4 hV4 V5 V6"
    When I press button "Reload I"
    Then I check if input value for "#log" match text "V4 hV4 V5 V6 hU3 I7 U7 V7"
    When I press button "Add U handler"
    When I press button "Reload V"
    Then I check if input value for "#log" match text "V4 hV4 V5 V6 hU3 I7 U7 V7 V8"

  Scenario: multiple handlers
    When I fill field using "#log" with ""
    When I press button "Add V handler"
    When I press button "Add V handler"
    When I press button "Reload V"
    Then I check if input value for "#log" match text "hV8 hV8 V9"

  Scenario: handler for child must be called first
    Given I am on "_unit-test/remove-observer.php"
    When I press button "Add I handler"
    When I press button "Add V handler"
    When I press button "Reload I"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0 hV0 hI0 I1 U1 V1"
    Given I am on "_unit-test/remove-observer.php"
    When I press button "Add V handler"
    When I press button "Add I handler"
    When I press button "Reload I"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0 hV0 hI0 I1 U1 V1"

  Scenario: remove handler
    Given I am on "_unit-test/remove-observer.php"
    When I press button "Add U handler"
    When I press button "Add V handler"
    When I press button "Reload I"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0 hU0 hV0 I1 U1 V1"
    Given I am on "_unit-test/remove-observer.php"
    When I press button "Add U handler"
    When I press button "Add V handler"
    When I press button "Remove last handler"
    When I press button "Reload I"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0 hU0 I1 U1 V1"

  Scenario: handler must be called for moved element
    Given I am on "_unit-test/remove-observer.php"
    When I press button "Add U handler"
    When I press button "Move U to J"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0 hU0"
    When I press button "Add U handler"
    When I press button "Reload J"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0 hU0 hU0 J1"

  Scenario: handler must not be called for readded element
    Given I am on "_unit-test/remove-observer.php"
    When I press button "Add U handler"
    When I press button "Readd U"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0"
    When I press button "Reload I"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0 hU0 I1 U1 V1"

  Scenario: abort API when owner is reloaded
    Given I am on "_unit-test/remove-observer.php"
    When I press button "Run slow API"
    Then I should see "Abort failed"
    Given I am on "_unit-test/remove-observer.php"
    When I press button "Run slow API & remove"
    Then I should not see "Abort failed"

  Scenario: abort SSE when owner is reloaded
    Given I am on "_unit-test/remove-observer.php"
    When I press button "Run slow SSE"
    When I wait "2000" ms
    Then I should see "Abort failed"
    Given I am on "_unit-test/remove-observer.php"
    When I press button "Run slow SSE & remove"
    When I wait "2000" ms
    Then I should not see "Abort failed"
