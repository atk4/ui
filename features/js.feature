Feature: JS
    Test javascript

 Background:
  Given I am on "js.php"

Scenario:
 Then I don't see button "Hidden Button"

Scenario:
 And I see button "Hide on click Button"
 When I press button "Hide on click Button"
 Then I don't see button "Hide on click Button"

Scenario:
 And I see button "B"
 When I press button "Hide button B"
 Then I don't see button "B"
 And I don't see button "Hide button B"


Scenario:
 And I see button "C"
 When I press button "Hide button C"
 Then I don't see button "C"
 And I don't see button "Hide button C"

Scenario:
 And I see button "Callback Test"
 When I press button "Callback Test"
 And  wait for callback
 Then Label changes to a number

Scenario:
 Given I am on "js.php"
 When I press button "failure"
 And  wait for callback
 Then Modal is open with text "Everything is bad"
