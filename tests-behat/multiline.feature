Feature: Multiline

  Scenario:
    Given I am on "form-control/multiline.php"
    When I fill field using "div[name=-atk_fp_multiline_item__qty] input" with "0"
    Then the "div[name=-atk_fp_multiline_item__total_sql]" element should contain "0"
    Then the "div[name=-atk_fp_multiline_item__total_php]" element should contain "0"
    When I fill field using "div[name=-atk_fp_multiline_item__qty] input" with "4"
    When I fill field using "div[name=-atk_fp_multiline_item__box] input" with "67"
    Then the "div[name=-atk_fp_multiline_item__total_sql]" element should contain "268"
    Then the "div[name=-atk_fp_multiline_item__total_php]" element should contain "268"
    When I select "Argentina" in lookup "//table//tr[1]//div.text[parent::div[@name='-atk_fp_multiline_item__country_id']]"
    When I press button "Save"
    Then Toast display should contain text "\"atk_fp_multiline_item__box\": \"67\", \"atk_fp_multiline_item__total_sql\": \"268\" }"
    Then Toast display should contain text "\"atk_fp_multiline_item__country_id\": \"10\", \"atk_fp_multiline_item__qty\": \"4\","
    Then Toast display should contain text "\"atk_fp_multiline_item__country_id\": \"223\", \"atk_fp_multiline_item__qty\": \"2\","

  Scenario: add row
    When I click using selector "//tfoot//button[i.plus.icon]"
    Then I should not see "Must not be empty"
    When I press button "Save"
    Then I should see "Must not be empty"
    When I fill field using "//tr[3]//div[@name='-atk_fp_multiline_item__item']/input" with "Paper"
    When I fill field using "//tr[3]//div[@name='-atk_fp_multiline_item__qty']/input" with "3"
    When I fill field using "//tr[3]//div[@name='-atk_fp_multiline_item__box']/input" with "5"
    Then I check if text in "//tr[3]//div[@name='-atk_fp_multiline_item__total_sql']" match text "15"
    Then I check if text in "//tr[3]//div[@name='-atk_fp_multiline_item__total_php']" match text "15"
    When I press button "Save"
    Then Toast display should contain text "\"atk_fp_multiline_item__box\": \"5\", \"atk_fp_multiline_item__total_sql\": \"15\" } ]"
    Then I should not see "Must not be empty"

  Scenario: delete row
    When I click using selector "//tr[3]//input[@type='checkbox']"
    When I click using selector "//tfoot//button[i.trash.icon]"
    When I press button "Save"
    Then Toast display should contain text "\"atk_fp_multiline_item__box\": \"100\", \"atk_fp_multiline_item__total_sql\": \"200\" } ]"

  Scenario: delete all rows
    When I click using selector "//thead//input[@type='checkbox']"
    When I click using selector "//tfoot//button[i.trash.icon]"
    When I press button "Save"
    Then Toast display should contain text "[]"
