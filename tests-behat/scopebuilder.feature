Feature: ScopeBuilder
  Testing scope builder

  Scenario:
    Given I am on "_unit-test/scope-builder.php"
    Then text rule "project_name" operator is "matches regular expression" and value is "[a-zA-Z]"
    Then hasRef rule "client_country_iso" operator is "equals" and value is "Brazil"
#    Then date rule "start_date" operator is "is on" and value is "Oct 22, 2020"
    Then date rule "start_date" operator is "is on" and value is "2020-10-22"
    Then date rule "finish_time" operator is "is not on" and value is "22:22:00"