Feature: Lookup
  Testing Lookup control

  Scenario: Testing lookup in modal
    Given I am on "_unit-test/lookup.php"
    Then I press button "Edit"
    # '405bba7f' = substr(md5('product_category'), 0, 8)
    Then I select value "Dairy" in lookup "atk_fp_product__405bba7f_id"
    # '6f3c91cf' = substr(md5('product_sub_category'), 0, 8)
    Then I select value "Yogourt" in lookup "atk_fp_product__6f3c91cf_id"
    Then I press modal button "Save"
    Then Toast display should contain text 'Dairy - Yogourt'

  Scenario: Testing lookup in VirtualPage
    Given I am on "_unit-test/lookup-virtual-page.php"
    Then I press menu button "Add Category" using class "atk-grid-menu"
    Then I select value "Beverages" in lookup "category"
    Then I press Modal button "Save"
    Then Toast display should contain text "Beverages"

  Scenario: Testing lookup add
    Given I am on "form-control/lookup.php"
    Then I press button "Add New"
    When I fill in "atk_fp_country__name" with "New country"
    When I fill in "atk_fp_country__iso" with "AA"
    When I fill in "atk_fp_country__iso3" with "AAA"
    When I fill in "atk_fp_country__numcode" with "88"
    # '50ce262c' = substr(md5('phonecode'), 0, 8)
    When I fill in "atk_fp_country__50ce262c" with "8"
    Then I press Modal button "Save"
    Then Toast display should contain text "Form submit!"
