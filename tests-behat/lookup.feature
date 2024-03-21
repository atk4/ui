Feature: Lookup

  Scenario: Testing lookup in modal
    Given I am on "_unit-test/lookup.php"
    Then I press button "Edit"
    Then I select value "Dairy" in lookup "atk_fp_product__product_category_id"
    # '6f3c91cf51e02fd5' = substr(md5('product_sub_category'), 0, 16)
    Then I select value "Yogourt" in lookup "atk_fp_product__6f3c91cf51e02fd5_id"
    Then I press modal button "Save"
    Then Toast display should contain text 'Dairy - Yogourt'

  Scenario: Testing lookup in VirtualPage
    Given I am on "_unit-test/lookup-virtual-page.php"
    Then I press button "Add Category"
    Then I select value "Beverages" in lookup "category"
    Then I press Modal button "Save"
    Then Toast display should contain text "Beverages"

  Scenario: Testing lookup add
    Given I am on "form-control/lookup.php"
    Then I check if text in "//div.text[../input[@name='country2']]" match text ""
    Then I press button "Add New"
    When I fill in "atk_fp_country__name" with "Plusia"
    When I fill in "atk_fp_country__iso" with "AA"
    When I fill in "atk_fp_country__iso3" with "AAA"
    When I fill in "atk_fp_country__numcode" with "88"
    When I fill in "atk_fp_country__phonecode" with "8"
    Then I press Modal button "Save"
    Then Toast display should contain text 'Country action "add" with "Plusia" entity was executed.'
    Then I check if text in "//div.text[../input[@name='country2']]" match text "Plusia"
