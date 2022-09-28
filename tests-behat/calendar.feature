Feature: Calendar

  Scenario:
    Given I am on "_unit-test/calendar-input.php"

  Scenario: field using format M d, Y
    Then I set calendar input name 'field' with value 'Jan 01, 2021'
    When I press button "field"
    Then I should see "Jan 01, 2021"
    Then I hide js modal

  Scenario: input using format Y-m-d
    Then I set calendar input name 'date_ymd' with value '2021-01-01'
    When I press button "date_ymd"
    Then I should see "2021-01-01"
    Then I hide js modal

  Scenario: input using format H:i:s
    Then I set calendar input name 'time_24hr' with value '22:22:22'
    When I press button "time_24hr"
    Then I should see "22:22:22"
    Then I hide js modal

  Scenario: input using format G:i A
    Then I set calendar input name 'time_am' with value '11:22 AM'
    When I press button "time_am"
    Then I should see "11:22 AM"
    Then I hide js modal

  Scenario: input using format Y-m-d (H:i:s)
    Then I set calendar input name 'datetime' with value '2021-01-01 (22:22:22)'
    When I press button "datetime"
    Then I should see "2021-01-01 (22:22:22)"
    Then I hide js modal
