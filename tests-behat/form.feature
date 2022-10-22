Feature: Form

  Scenario: test form response
    Given I am on "form/form.php"
    When I fill in "email" with "foo@bar"
    When I press button "Subscribe"
    Then I should see "Subscribed foo@bar to newsletter."

    When I press button "Compare Date"
    Then I should see "Date field vs control:"
    Then I hide js modal

    When I click tab with title "Handler Output"
    When I fill in "email1" with "foo@bar"
    When I press button "Save1"
    Then I should see "some error action"

    When I fill in "email2" with "foo@bar"
    When I press button "Save2"
    Then I should see "form was successful"

    When I fill in "email3" with "foo@bar"
    When I press button "Save3"
    Then I should see "some header"
    Then I should see "some text"
    Then I hide js modal

    When I fill in "email5" with "foo@bar"
    When I press button "Save5"
    Then input "email5" value should start with "random is"

  Scenario: form exception is displayed
    When I click tab with title "Handler Safety"
    When I press button "SaveE1"
    Then Modal is open with text "Error: Cannot use object of type stdClass as array"
    Then I hide js modal
    When I press button "SaveE2"
    Then Modal is open with text "Atk4\Core\Exception: Test exception I."
    Then I hide js modal
    When I press button "Modal Test"
    Then I check if input value for "#mf input[name='email']" match text ""
    When I fill Modal field "email" with "ee"
    Then I check if input value for "#mf input[name='email']" match text "ee"
    When I press Modal button "Save"
    Then Modal is open with text "Atk4\Core\Exception: Test exception II."
    Then I hide js modal
    Then I check if input value for "#mf input[name='email']" match text "ee"
    Then I hide js modal
    When I press button "Modal Test"
    Then I check if input value for "#mf input[name='email']" match text ""
    When I press Modal button "Save"
    Then Modal is open with text "Atk4\Core\Exception: Test exception II."
    Then I hide js modal
    When I press Modal button "Save"
    Then Modal is open with text "Atk4\Core\Exception: Test exception II."
