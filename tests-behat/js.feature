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

    # "xpath(//div.ui.header[text()[normalize-space()='Callbacks']]/following-sibling::div.ui.button[1])" is long for nice CI output
    When I check if text in "xpath(//div.ui.button[8])" match text "Callback Test"
    When I press button "Callback Test"
    # "xpath(//div.ui.header[text()[normalize-space()='Callbacks']]/following-sibling::div.ui.button[1])" is long for nice CI output
    Then I check if text in "xpath(//div.ui.button[8])" match regex "~^\d+$~"

  Scenario: JsCallback exception is displayed
    When I press button "failure"
    Then Modal is open with text "Atk4\Ui\Exception: Everything is bad"
    Then I hide js modal
    When I press button "failure"
    Then Modal is open with text "Atk4\Ui\Exception: Everything is bad"
