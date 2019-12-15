Feature: Form
    In order to have an awesome PHP UI Framework
    As a responsible open-source developer
    I need to write tests for our demo pages

Scenario:
 Given I am on "form.php"
 When I fill in "email" with "foo@bar"
 And I press button "Subscribe"
 And form submits
 Then I should see "Subscribed foo@bar to newsletter."

Scenario:
 Given I am on "form.php"
 ##When I fill in "Date1" with "foo@bar"
 And I press button "Compare Date"
 And form submits
 Then I should see "Direct Output Detected"
