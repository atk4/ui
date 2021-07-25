Feature: UserAction
  Testing UI executor UserAction and UserConfirmation modal.

  Scenario:
    Given I am on "data-action/jsactions2.php"
    And I press button "Callback"
    Then Toast display should contains text "callback execute using country"

  Scenario:
    And I press button "Argument"
    Then Modal is open with text "Age" in tag "label"
    When I fill Modal field "age" with "22"
    Then I press Modal button "Argument"
    Then Toast display should contains text "22 is old enough to visit"

  Scenario:
    And I press button "User Confirmation"
    And I press Modal button "Ok"
    Then Toast display should contains text "Confirm country"

  Scenario:
    And I press button "Multi Step"
    Then Modal is open with text "Age" in tag "label"
    When I fill Modal field "age" with "22"
    Then I press Modal button "Next"
    Then I press Modal button "Next"
    Then Modal is open with text "Gender = m / Age = 22"
    Then I press Modal button "Multi Step"
    Then Toast display should contains text "Thank you Mr. at age 22"

  Scenario: testing VpExecutor
    Given I am on "data-action/jsactions-vp.php"
    And I press button "Argument"
    Then I should see "Age"
    When I fill in "age" with "22"
    Then I press button "Argument"

  Scenario: testing return
    Then I should see "Assign Model action to button event"

  Scenario: testing multi in virtual page
    And I press button "Multi Step"
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
    And I press button "Argument"
    Then Panel is open with text "Age" in tag "label"
    When I fill Panel field "age" with "22"
    Then I press Panel button "Argument"
    Then Toast display should contains text "22 is old enough to visit"

  Scenario: testing multi in panel
    And I press button "Multi Step"
    Then Panel is open with text "Age" in tag "label"
    When I fill Panel field "age" with "22"
    Then I press Panel button "Next"
    Then I press Panel button "Next"
    Then Panel is open with text "Gender = m / Age = 22"
    Then I press Panel button "Multi Step"
    Then Toast display should contains text "Thank you Mr. at age 22"
