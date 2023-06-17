Feature: Calendar

  Scenario:
    Given I am on "form-control/calendar.php"
    When I fill in "date" with "Jan 3, 2033"
    Then I check if input value for "input[name='date']" match text "Jan 3, 2033"
    When I fill in "time" with "21:23:59.205603"
    Then I check if input value for "input[name='time']" match text "21:23:59.205603"
    When I fill in "datetime" with "Jun 29, 2020 1:2:3.0010"
    Then I check if input value for "input[name='datetime']" match text "Jun 29, 2020 01:02:03.001"
    When I fill in "date_action" with "Dec 8, 2050"
    When I press button "Save"
    Then Toast display should contain text "Jan 3, 2033, 21:23:59.205603, Jun 29, 2020 01:02:03.001, Dec 8, 2050"

    When I fill in "time" with "21:23:00"
    Then I check if input value for "input[name='time']" match text "21:23"
    When I fill in "datetime" with "Jun 29, 2020 1:2:0.000001"
    Then I check if input value for "input[name='datetime']" match text "Jun 29, 2020 01:02:00.000001"
    When I fill in "datetime" with "Jun 29, 2020 1:2:0"
    Then I check if input value for "input[name='datetime']" match text "Jun 29, 2020 01:02"
    When I press button "Clear"
    Then I check if input value for "input[name='date_action']" match text ""
    When I press button "Save"
    Then Toast display should contain text "Jan 3, 2033, 21:23, Jun 29, 2020 01:02, empty"
