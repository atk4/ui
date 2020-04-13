Feature: Lookup
  Testing Lookup fields

  Scenario:
    Given I am on "lookup.php"
#    Need db value to work
#    Then I select value "Albania" in lookup "country1"
#    Then I select value "Albania" in lookup "country2"
#    Then I select value "Albania" in lookup "country3"
    And I press button "Save"
    And form submits
    And wait for callback
    Then I should see "Select:"
#    And I should see "Albania Albania Albania"
