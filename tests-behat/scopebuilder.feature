Feature: ScopeBuilder
  Testing scope builder

  Scenario: test ScopeBuilder rendering of model scope
    Given I am on "_unit-test/scope-builder.php"
    # 'a3b68d0c' = substr(md5('project_budget'), 0, 8)
    Then rule "atk_fp_stat__a3b68d0c" operator is ">=" and value is "1000"
    # 'ff68957f' = substr(md5('project_name'), 0, 8)
    Then rule "atk_fp_stat__ff68957f" operator is "matches regular expression" and value is "[a-zA-Z]"
    Then select rule "atk_fp_stat__currency" operator is "equals" and value is "USD"
    # '205293a6' = substr(md5('client_country_iso'), 0, 8)
    Then reference rule "atk_fp_stat__205293a6" operator is "equals" and value is "Brazil"
    # 'eadbb911' = substr(md5('start_date'), 0, 8)
    Then date rule "atk_fp_stat__eadbb911" operator is "is on" and value is "Oct 22, 2020"
    # '80fa221d' = substr(md5('finish_time'), 0, 8)
    Then date rule "atk_fp_stat__80fa221d" operator is "is not on" and value is "22:22"
    # '5e550ea8' = substr(md5('is_commercial'), 0, 8)
    Then bool rule "atk_fp_stat__5e550ea8" has value "No"
    Then I check if input value for "qb" match text in "p.atk-expected-input-result"
    And I press button "Save"
    # TODO uncomment once "Object serialization is not supported" is fixed
    # Then I check if text in "p.atk-expected-word-result" match text in ".atk-scope-builder-response"

  Scenario: test ScopeBuilder query string to model scope
    Given I am on "_unit-test/scope-builder-to-query.php"
    Then container ".ui.table tbody tr" should display "1" item(s)
    Then I should see "Milk 1%"
    Then I should see "Dairy"
    Then I should see "Lowfat Milk"
