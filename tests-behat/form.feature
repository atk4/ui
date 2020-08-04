Feature: Form
    Testing forms

Scenario: test form response
  Given I am on "form/form.php"
  When I fill in "email" with "foo@bar"
  And I press button "Subscribe"
  Then I should see "Subscribed foo@bar to newsletter."

  And I press button "Compare Date"
  Then I should see "Date field vs control:"
  Then I hide js modal

  And I click tab with title "Handler Output"
  When I fill in "email1" with "foo@bar"
  And I press button "Save1"
  Then I should see "some error action"

  When I fill in "email2" with "foo@bar"
  And I press button "Save2"
  Then I should see "form was successful"

  When I fill in "email3" with "foo@bar"
  And I press button "Save3"
  Then I should see "some header"
  And I should see "some text"
  Then I hide js modal

  When I fill in "email5" with "foo@bar"
  And I press button "Save5"
  Then the "email5"  should start with "random is"
