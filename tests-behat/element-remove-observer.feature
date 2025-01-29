Feature: Element remove observer

  Scenario:
    Given I am on "_unit-test/element-remove-observer.php"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0"
    When I press button "Reload V"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0 V1"
    When I press button "Reload V"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0 V1 V2"
    When I press button "Reload I"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0 V1 V2 I3 U3 V3"
    When I fill field using "#log" with ""
    When I press button "Add A handler"
    When I press button "Reload V"
    Then I check if input value for "#log" match text "V4"
    When I press button "Add V handler"
    When I press button "Reload V"
    Then I check if input value for "#log" match text "V4 hV4 V5"
    When I press button "Reload V"
    Then I check if input value for "#log" match text "V4 hV4 V5 V6"

  Scenario: multiple handlers
    When I fill field using "#log" with ""
    When I press button "Add V handler"
    When I press button "Add V handler"
    When I press button "Reload V"
    Then I check if input value for "#log" match text "hV6 hV6 V7"

  Scenario: handler for child must be called first
    Given I am on "_unit-test/element-remove-observer.php"
    When I press button "Add I handler"
    When I press button "Add V handler"
    When I press button "Reload I"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0 hV0 hI0 I1 U1 V1"
    Given I am on "_unit-test/element-remove-observer.php"
    When I press button "Add V handler"
    When I press button "Add I handler"
    When I press button "Reload I"
    Then I check if input value for "#log" match text "A0 I0 U0 V0 J0 hV0 hI0 I1 U1 V1"
