Feature: Form
    In order to have an awesome PHP UI Framework
    As a responsible open-source developer
    Second tab of form.php should be tested

Background:
    Given I am on "form.php"
    And I click link "Handler Output" 

Scenario:
 When I fill in "email1" with "foo@bar"
 And I press button "Save1"
 And form submits
 Then I wait for send action using ".ui.basic.red.pointing.prompt.label.transition.visible"
 Then I should see "some error action"

Scenario:
 When I fill in "email2" with "foo@bar"
 And I press button "Save2"
 And form submits
 Then I wait for send action using ".atk-form-success"
 Then I should see "form was successful"

Scenario:
 When I fill in "email3" with "foo@bar"
 And I press button "Save3"
 And form submits
 Then I wait for send action using ".atk-callback-response"
 Then I should see "some header"
 And I should see "some text"

Scenario:
 When I fill in "email5" with "foo@bar"
 And I press button "Save5"
 And form submits
 Then the "email5" field should start with "random is"

