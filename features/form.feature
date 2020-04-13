Feature: Form
    Testing forms

Scenario: test form response
  Given I am on "form.php"
  When I fill in "email" with "foo@bar"
  And I press button "Subscribe"
  And form submits
  And wait for callback
  Then I should see "Subscribed foo@bar to newsletter."

Scenario: test form direct output
  Given I am on "form.php"
  And I press button "Compare Date"
  And form submits
  And wait for callback
  Then I should see "Direct Output Detected"

