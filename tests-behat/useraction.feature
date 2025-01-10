Feature: UserAction executor and UserConfirmation modal

  Scenario:
    Given I am on "data-action/jsactions2.php"
    When I press button "Callback"
    Then Toast display should contain text "callback execute using country"

  Scenario:
    When I press button "Argument"
    Then Modal is open with text "Age" in selector "label"
    When I fill Modal field "age" with "22"
    When I press Modal button "Argument"
    Then Toast display should contain text "22 is old enough to visit"

  Scenario:
    When I press button "User Confirmation"
    When I press Modal button "Ok"
    Then Toast display should contain text "Confirm country"

  Scenario:
    When I press button "Multi Step"
    Then Modal is open with text "Age" in selector "label"
    When I fill Modal field "age" with "22"
    When I press Modal button "Next"
    When I press Modal button "Next"
    Then Modal is open with text "Gender = m / Age = 22"
    When I press Modal button "Multi Step"
    Then Toast display should contain text "Thank you Mr. at age 22"

  Scenario: testing VpExecutor
    Given I am on "data-action/jsactions-vp.php"
    When I press button "Argument"
    Then I should see "Age"
    When I fill in "age" with "22"
    When I press button "Argument"

  Scenario: testing return
    Then I should see "Assign Model action to button event"

  Scenario: testing multi in virtual page
    When I press button "Multi Step"
    Then I should see "Age"
    When I fill in "age" with "22"
    When I press button "Next"
    When I press button "Next"
    Then I should see "Gender = m / Age = 22"
    When I press button "Multi Step"

  Scenario: testing return
    Then I should see "Assign Model action to button event"

  Scenario: testing PanelExecutor
    Given I am on "data-action/jsactions-panel.php"
    When I press button "Argument"
    Then Panel is open with text "Age" in selector "label"
    When I fill Panel field "age" with "22"
    When I press Panel button "Argument"
    Then Toast display should contain text "22 is old enough to visit"

  Scenario: testing multi in panel
    When I press button "Multi Step"
    Then Panel is open with text "Age" in selector "label"
    When I fill Panel field "age" with "22"
    When I press Panel button "Next"
    When I press Panel button "Next"
    Then Panel is open with text "Gender = m / Age = 22"
    When I press Panel button "Multi Step"
    Then Toast display should contain text "Thank you Mr. at age 22"

  Scenario: testing JsCallbackExecutor with form input argument
    Given I am on "data-action/jsactions.php"
    When I fill field using "//input[../div[text()='Greet']]" with "Laura"
    When I press button "Greet"
    Then Toast display should contain text "Hello Laura"
    Given I am on "_unit-test/useraction-input-callback.php"
    When I fill field using "//input[../div[text()='Greet Integer']]" with "2_3"
    When I press button "Greet Integer"
    Then Toast display should contain text "Hello II 23"
    When I fill field using "//input[../div[text()='Greet Wrapped ID']]" with "24"
    When I press button "Greet Wrapped ID"
    Then Toast display should contain text "Hello III 24"

  Scenario: testing JsCallbackExecutor with form input argument - validation, exception is displayed
    When I fill field using "//input[../div[text()='Greet Integer']]" with "x"
    When I press button "Greet Integer"
    # TODO https://github.com/atk4/data/blob/5.2.0/src/Persistence.php#L496 should be unrolled
    # like in https://github.com/atk4/ui/blob/5.2.0/src/Form.php#L448
    Then Modal is open with text "Atk4\Data\Exception: Must be numeric"
    When I hide js modal
    When I fill field using "//input[../div[text()='Greet Integer']]" with ""
    When I press button "Greet Integer"
    # TODO "required" must be honored

  Scenario: testing JsCallbackExecutor in grid menu
    Given I am on "data-action/jsactionsgrid.php"
    When I click using selector "//tr[td[text()='Argentina']]//div.ui.dropdown[div[text()='Actions...']]"
    Then No toast should be displayed
    When I click using selector "//tr[td[text()='Argentina']]//div.ui.dropdown[div[text()='Actions...']]//div.menu/div[text()='Callback']"
    Then Toast display should contain text "Success: callback execute using country Argentina"

  Scenario: validate user action earlier than before execute, exception is displayed
    Given I am on "_unit-test/useraction-no-id-arg.php"
    When I press button "Disabled"
    Then Modal is open with text "Atk4\Data\Exception: User action is disabled"
    When I hide js modal
    When I press button "Add"
    Then Modal is open with text "Add Country"
    When I hide js modal
    When I press button "Edit"
    Then Modal is open with text "Atk4\Data\Exception: User action can be executed on loaded entity only"
    When I hide js modal
    When I press button "Delete"
    When I press Modal button "Ok"
    Then Modal is open with text "Atk4\Data\Exception: User action can be executed on loaded entity only"
    When I hide js modal
    When I press button "Callback"
    Then Modal is open with text "Atk4\Data\Exception: User action can be executed on loaded entity only"
    When I hide js modal
    When I press button "Preview"
    Then Modal is open with text "Atk4\Data\Exception: User action can be executed on loaded entity only"
    When I hide js modal
    When I press button "Argument"
    Then Modal is open with text "Atk4\Data\Exception: User action can be executed on loaded entity only"
    When I hide js modal
    When I press button "User Confirmation"
    Then Modal is open with text "Atk4\Data\Exception: User action can be executed on loaded entity only"
    When I hide js modal
    When I press button "Multi Step"
    Then Modal is open with text "Atk4\Data\Exception: User action can be executed on loaded entity only"
    When I hide js modal
