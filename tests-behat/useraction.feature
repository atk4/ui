Feature: UserAction executor and UserConfirmation modal

  Scenario:
    Given I am on "data-action/jsactions2.php"
    When I press button "Callback"
    Then Toast display should contain text "callback execute using country"

  Scenario:
    When I press button "Argument"
    Then Modal is open with text "Age" in selector "label"
    When I fill Modal field "age" with "22"
    Then I press Modal button "Argument"
    Then Toast display should contain text "22 is old enough to visit"

  Scenario:
    When I press button "User Confirmation"
    When I press Modal button "Ok"
    Then Toast display should contain text "Confirm country"

  Scenario:
    When I press button "Multi Step"
    Then Modal is open with text "Age" in selector "label"
    When I fill Modal field "age" with "22"
    Then I press Modal button "Next"
    Then I press Modal button "Next"
    Then Modal is open with text "Gender = m / Age = 22"
    Then I press Modal button "Multi Step"
    Then Toast display should contain text "Thank you Mr. at age 22"

  Scenario: testing VpExecutor
    Given I am on "data-action/jsactions-vp.php"
    When I press button "Argument"
    Then I should see "Age"
    When I fill in "age" with "22"
    Then I press button "Argument"

  Scenario: testing return
    Then I should see "Assign Model action to button event"

  Scenario: testing multi in virtual page
    When I press button "Multi Step"
    Then I should see "Age"
    When I fill in "age" with "22"
    Then I press button "Next"
    Then I press button "Next"
    Then I should see "Gender = m / Age = 22"
    Then I press button "Multi Step"

  Scenario: testing return
    Then I should see "Assign Model action to button event"

  Scenario: testing PanelExecutor
    Given I am on "data-action/jsactions-panel.php"
    When I press button "Argument"
    Then Panel is open with text "Age" in selector "label"
    When I fill Panel field "age" with "22"
    Then I press Panel button "Argument"
    Then Toast display should contain text "22 is old enough to visit"

  Scenario: testing multi in panel
    When I press button "Multi Step"
    Then Panel is open with text "Age" in selector "label"
    When I fill Panel field "age" with "22"
    Then I press Panel button "Next"
    Then I press Panel button "Next"
    Then Panel is open with text "Gender = m / Age = 22"
    Then I press Panel button "Multi Step"
    Then Toast display should contain text "Thank you Mr. at age 22"

  Scenario: testing JsCallbackExecutor with form input argument
    Given I am on "data-action/jsactions.php"
    When I fill field using "//input[../div[text()='Greet']]" with "Laura"
    When I press button "Greet"
    Then Toast display should contain text "Hello Laura"
