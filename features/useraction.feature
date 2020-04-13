Feature: UserAction
  Testing UI executor UserAction and UserConfirmation modal.

  Scenario: :
    Given I am on "jsactions2.php"

    And I press button "Callback"
    And wait for callback
    Then Toast display should contains text "callback execute using country"

    And I press button "Argument"
    And wait for callback
    Then Modal is showing text "Age" inside tag "label"
    When I fill in "age" with "22"
    Then I press button "Edit Argument"
    And wait for callback
    Then Toast display should contains text "22 is old enough to visit"

    And I press button "User Confirmation"
    And wait for callback
    Then Modal is open with text "A confirmation is required for Country"
    Then I press button "Ok"
    And wait for callback
    Then Toast display should contains text "Confirm country"

#    Need to reload page for this step
  Scenario: :
    Given I am on "jsactions2.php"
    And I press button "Argument/Field/Preview"
    And wait for callback
    Then Modal is showing text "Age" inside tag "label"
    When I fill in "age" with "22"
    Then I press Modal button "Next"
    And wait for callback
    Then I press Modal button "Next"
    And wait for callback
    Then Modal is open with text "Gender = m / Age = 22"
    Then I press Modal button "Multi Step"
    And wait for callback
    Then Toast display should contains text "Thank you Mr. at age 22"
