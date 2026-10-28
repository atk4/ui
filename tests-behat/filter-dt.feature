Feature: Table Filter (Data Types)

  Scenario:
    Given I am on "collection/tablefilter-dt.php"
    When I press button "Clear Filters"
    Then I should see "Agile Core"

    # Testing remove icon - no op = no extra fields
    #When I click filter column name "atk_fp_stat__project_name"
    #Then I should see a "//div.popup[1]//div.field//input[@name='value']" element
    #When I click using selector "//div.popup[1]//div.field//input[@name='op']//..//i.remove"
    #Then I should not see a "//div.popup[1]//div.field//input[@name='value']" element

    When I click filter column name "atk_fp_stat__finish_date"
    When I select "Is before" in lookup "//div.popup[7]//input[@name='op']"
    When I select "Today" in lookup "//div.popup[7]//input[@name='value']"
    When I click using selector "//div.popup[7]//div[text()='Set']"
    Then I should see "Agile Core"

    When I click filter column name "atk_fp_stat__finish_date"
    When I select "Is on or after" in lookup "//div.popup[7]//input[@name='op']"
    When I select "Tomorrow" in lookup "//div.popup[7]//input[@name='value']"
    When I click using selector "//div.popup[7]//div[text()='Set']"
    Then I should not see "Agile Core"

