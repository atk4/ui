Feature: Form
    Testing forms.

Background:
    Given I am on "form.php"
    And I wait 500
    And I click link "Handler Output" 

Scenario: test form validation
  When I fill in "email1" with "foo@bar"
  And I press button "Save1"
  And form submits
  And wait for callback
  Then I should see "some error action"

Scenario: test form success
  When I fill in "email2" with "foo@bar"
  And I press button "Save2"
  And form submits
  And wait for callback
  Then I should see "form was successful"

Scenario: test form response
  When I fill in "email3" with "foo@bar"
  And I press button "Save3"
  And form submits
  And wait for callback
  Then I should see "some header"
  And I should see "some text"

Scenario: test input javascript via callback
  When I fill in "email5" with "foo@bar"
  And I press button "Save5"
  And wait for callback
  Then the "email5"  should start with "random is"
