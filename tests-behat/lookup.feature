Feature: Lookup
  Testing Lookup control

  Scenario:
    Given I am on "_unit-test/lookup.php"

  Scenario: Testing lookup in modal
    Then I press button "Edit"
    Then I select value "Dairy" in lookup "product_category_id"
    Then I select value "Yogourt" in lookup "product_sub_category_id"
    Then I press modal button "Save"
    Then Toast display should contains text 'Dairy - Yogourt'
