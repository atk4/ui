Feature: Multiline

  Scenario:
    Given I am on "form-control/multiline.php"
    When I fill in "-atk_fp_multiline_item__qty" with "0"
    Then the "div[name=-atk_fp_multiline_item__total_sql]" element should contain "0"
    Then the "div[name=-atk_fp_multiline_item__total_php]" element should contain "0"
    When I fill in "-atk_fp_multiline_item__qty" with "2"
    When I fill in "-atk_fp_multiline_item__box" with "67"
    Then the "div[name=-atk_fp_multiline_item__total_sql]" element should contain "134"
    Then the "div[name=-atk_fp_multiline_item__total_php]" element should contain "134"
    Then I press button "Save"
    Then Toast display should contain text '"atk_fp_multiline_item__qty": 2, "atk_fp_multiline_item__box": 67, "atk_fp_multiline_item__total_sql": 134 }'
