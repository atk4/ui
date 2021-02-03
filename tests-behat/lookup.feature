Feature: Lookup
  Testing Lookup control

  Scenario: Testing lookup in modal
    Given I am on "_unit-test/lookup.php"
    Then I press button "Edit"
    Then I select value "Dairy" in lookup "atk_fp_product__product_category_id"
    Then I select value "Yogourt" in lookup "atk_fp_product__product_sub_category_id"
    Then I press button "EditMe"
    Then Toast display should contains text 'Dairy - Yogourt'

  Scenario: Testing lookup in VirtualPage
    Given I am on "_unit-test/lookup-virtual-page.php"
    Then I press menu button "Add Category" using class "atk-grid-menu"
    Then I select value "Beverages" in lookup "category"
    Then I press Modal button "Save"
    Then Toast display should contains text 'Beverages'
