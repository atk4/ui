Feature: Filter
    Testing table filter

Scenario:
 Given I am on "collection/tablefilter.php"
 Then I should see "Clear Filters"
 Then I click filter column name "name"
 When I fill in "value" with "united kingdom"
 Then I press button "Set"
 Then I should see "United Kingdom"
 Then I press menu button "Clear Filters" using class "atk-grid-menu"
 Then I should see "Australia"
