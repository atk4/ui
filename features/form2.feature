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
 Then I should see "some error action"

