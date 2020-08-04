Feature: JS
 Test javascript

 Scenario:
  Given I am on "javascript/js.php"

  Then I don't see button "Hidden Button"

  And I see button "Hide on click Button"
  When I press button "Hide on click Button"
  Then I don't see button "Hide on click Button"

  And I see button "B"
  When I press button "Hide button B"
  Then I don't see button "B"
  And I don't see button "Hide button B"

  And I see button "C"
  When I press button "Hide button C"
  Then I don't see button "C"
  And I don't see button "Hide button C"

  When I press button "Callback Test"
  Then Label changes to a number

  When I press button "failure"
  Then Modal is open with text "Everything is bad"
