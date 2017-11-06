Feature: Form
    In order to have an awesome PHP UI Framework
    As a responsible open-source developer
    I need to write tests for our demo pages

Scenario:
 Given I am on "form.php"
 When I fill in "email" with "foo@bar"
 And I press button "Subscribe"
 Then I should see "Subscribed foo@bar to newsletter."

