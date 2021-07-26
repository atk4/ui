Feature: ScopeBuilder
  Testing scope builder

  Scenario: test ScopeBuilder rendering of model scope
    Given I am on "_unit-test/scope-builder.php"
    Then rule "atk_fp_stat__project_budget" operator is ">=" and value is "1000"
    Then rule "atk_fp_stat__project_name" operator is "matches regular expression" and value is "[a-zA-Z]"
    Then select rule "atk_fp_stat__currency" operator is "equals" and value is "USD"
    Then reference rule "atk_fp_stat__client_country_iso" operator is "equals" and value is "Brazil"
    Then date rule "atk_fp_stat__start_date" operator is "is on" and value is "Oct 22, 2020"
    Then date rule "atk_fp_stat__finish_time" operator is "is not on" and value is "22:22"
    Then bool rule "atk_fp_stat__is_commercial" has value "No"
    Then I check if input value for "qb" match text in "p.atk-expected-input-result"
    And I press button "Save"
    Then I check if text in "p.atk-expected-word-result" match text in ".atk-scope-builder-response"

  Scenario: test ScopeBuilder query string to model scope
    Given I am on "_unit-test/scope-builder-to-query.php"
    Then container ".ui.table tbody tr" should display "1" item(s)
    Then I should see "Milk 1%"
    Then I should see "Dairy"
    Then I should see "Lowfat Milk"
