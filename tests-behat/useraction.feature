Feature: UserAction
  Testing UI executor UserAction and UserConfirmation modal.

  Scenario:
    Given I am on "collection/jsactions2.php"
    And I press button "Callback"
    Then Toast display should contains text "callback execute using country"

    #    Need to reload page for this step
  Scenario:
    Given I am on "collection/jsactions2.php"
    And I press button "Argument"
    Then Modal is showing text "Age" inside tag "label"
    When I fill in "age" with "22"
    Then I press button "Edit Argument"
    Then Toast display should contains text "22 is old enough to visit"

    #    Need to reload page for this step
  Scenario:
    Given I am on "collection/jsactions2.php"
    And I press button "User Confirmation"
    And I press Modal button "Ok"
    Then Toast display should contains text "Confirm country"

#    Need to reload page for this step
  Scenario:
    Given I am on "collection/jsactions2.php"
    And I press button "Argument/Field/Preview"
    Then Modal is showing text "Age" inside tag "label"
    When I fill in "age" with "22"
    Then I press Modal button "Next"
    Then I press Modal button "Next"
    Then Modal is open with text "Gender = m / Age = 22"
    Then I press Modal button "Multi Step"
    Then Toast display should contains text "Thank you Mr. at age 22"
