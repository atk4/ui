Feature: JS
    In order to have an awesome PHP UI Framework
    As a responsible open-source developer
    I need to write tests for our demo pages

    #Scenario:
        # Given I am on "js.php"
 # Then I don't see button "Hidden Button" 

Scenario:
 Given I am on "js.php"
 And I see button "Hide on click Button"
 When I press button "Hide on click Button"
 Then I don't see button "Hide on click Button" 

Scenario:
 Given I am on "js.php"
 And I see button "B"
 When I press button "Hide button B"
 Then I don't see button "B"
 And I don't see button "Hide button B"

 
Scenario:
 Given I am on "js.php"
 And I see button "C"
 When I press button "Hide button C"
 Then I don't see button "C"
 And I don't see button "Hide button C"

Scenario:
 Given I am on "js.php"
 And I see button "Callback Test"
 When I press button "Callback Test"
 Then Label changes to a number

#Scenario:
 #Given I am on "js.php"
 #When I press button "failure"
 #Then Modal opens with text "Everything is bad"

