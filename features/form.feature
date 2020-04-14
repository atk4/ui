Feature: Form
    Testing forms

Scenario: test form response
  Given I am on "form.php"
  When I fill in "email" with "foo@bar"
  And I press button "Subscribe"
  And form submits
  And wait for callback
  Then I should see "Subscribed foo@bar to newsletter."

  And I press button "Compare Date"
  And form submits
  And wait for callback
  Then I should see "Direct Output Detected"
  Then I hide js modal

  And I click tab with title "Handler Output"
  When I fill in "email1" with "foo@bar"
  And I press button "Save1"
  And form submits
  And wait for callback
  Then I should see "some error action"

  When I fill in "email2" with "foo@bar"
  And I press button "Save2"
  And form submits
  And wait for callback
  Then I should see "form was successful"

  When I fill in "email3" with "foo@bar"
  And I press button "Save3"
  And form submits
  And wait for callback
  Then I should see "some header"
  And I should see "some text"
  Then I hide js modal

  When I fill in "email5" with "foo@bar"
  And I press button "Save5"
  And wait for callback
  Then the "email5"  should start with "random is"
