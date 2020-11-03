Feature: ScopeBuilder
  Testing scope builder

  Scenario:
    Given I am on "_unit-test/scope-builder.php"
    Then rule "project_budget" operator is ">=" and value is "1000"
    Then rule "project_name" operator is "matches regular expression" and value is "[a-zA-Z]"
    Then reference rule "client_country_iso" operator is "equals" and value is "Brazil"
    Then date rule "start_date" operator is "is on" and value is "Oct 22, 2020"
    Then date rule "finish_time" operator is "is not on" and value is "22:22"
    Then bool rule "is_commercial" has value "No"
    Then I check if input "qb" match
    And I press button "Save"
    Then I check if word from data scope match
