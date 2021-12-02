Feature: Exception
  Test exception thrown

Scenario: JsCallback exception
  Given I am on "javascript/js.php"
    When I press button "failure"
    Then Modal is open with text "Critical Error"
