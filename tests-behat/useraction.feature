Feature: UserAction
  Testing UI executor UserAction and UserConfirmation modal.

  Scenario:
    Given I am on "data-action/jsactions2.php"
    And I press button "Callback"
    Then Toast display should contains text "callback execute using country"

    # need to reload page for this step
  Scenario:
    Given I am on "data-action/jsactions2.php"
    And I press button "Argument"
    Then Modal is showing text "Age" inside tag "label"
    When I fill in "age" with "22"
    Then I press Modal button "Argument"
    Then Toast display should contains text "22 is old enough to visit"

    # need to reload page for this step
  Scenario:
    Given I am on "data-action/jsactions2.php"
    And I press button "User Confirmation"
    And I press Modal button "Ok"
    Then Toast display should contains text "Confirm country"

    # need to reload page for this step
  Scenario:
    Given I am on "data-action/jsactions2.php"
    And I press button "Multi Step"
    Then Modal is showing text "Age" inside tag "label"
    When I fill in "age" with "22"
    Then I press Modal button "Next"
    Then I press Modal button "Next"
    Then Modal is open with text "Gender = m / Age = 22"
    Then I press Modal button "Multi Step"
    Then Toast display should contains text "Thank you Mr. at age 22"
