Feature: JS
  Test javascript

  Scenario:
    Given I am on "javascript/js.php"

    Then I don't see button "Hidden Button"

    Then I see button "Hide on click Button"
    When I press button "Hide on click Button"
    Then I don't see button "Hide on click Button"

    Then I see button "B"
    When I press button "Hide button B"
    Then I don't see button "B"
    Then I don't see button "Hide button B"

    Then I see button "C"
    When I press button "Hide button C"
    Then I don't see button "C"
    Then I don't see button "Hide button C"

    When I press button "Callback Test"
    Then Label changes to a number

  Scenario: JsCallback exception is displayed
    When I press button "failure"
    Then Modal is open with text "Atk4\Ui\Exception: Everything is bad"
