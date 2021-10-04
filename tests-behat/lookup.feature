Feature: Lookup
  Testing Lookup control

  Scenario: Testing lookup in modal
    Given I am on "_unit-test/lookup.php"
    Then I press button "Edit"
    Then I select value "Dairy" in lookup "atk_fp_product__product_category_id"
    Then I select value "Yogourt" in lookup "atk_fp_product__product_sub_category_id"
    Then I press modal button "Save"
    Then Toast display should contains text 'Dairy - Yogourt'

  Scenario: Testing lookup in VirtualPage
    Given I am on "_unit-test/lookup-virtual-page.php"
    Then I press menu button "Add Category" using class "atk-grid-menu"
    Then I select value "Beverages" in lookup "category"
    Then I press Modal button "Save"
    Then Toast display should contains text "Beverages"

  Scenario: Testing lookup add
    Given I am on "form-control/lookup.php"
    Then I press button "Add New"
    When I fill in "atk_fp_country__name" with "New country"
    When I fill in "atk_fp_country__iso" with "AA"
    When I fill in "atk_fp_country__iso3" with "AAA"
    When I fill in "atk_fp_country__numcode" with "88"
    When I fill in "atk_fp_country__phonecode" with "8"
    Then I press Modal button "Save"
    Then Toast display should contains text "Form submit!"
