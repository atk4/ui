Feature: Loader

  Scenario:
    Given I am on "interactive/loader2.php"
    When I click using selector "//td[text()='American Samoa']"
    Then I check if input value for "input[name='atk_fp_country__iso3']" match text "ASM"
    When I click using selector "//td[text()='Argentina']"
    Then I check if input value for "input[name='atk_fp_country__iso3']" match text "ARG"

  Scenario:
    Given I am on "collection/multitable.php"
    Then I should see "src"
    Then I should not see "HtmlTemplate"
    When I click using selector "//td[text()='src']"
    Then I should see "HtmlTemplate"
    Then I should not see "TagTree.php"
    When I click using selector "//td[text()='HtmlTemplate']"
    Then I should see "TagTree.php"
    Then I should not see "No records."
    When I click using selector "//td[text()='TagTree.php']"
    Then I should see "src"
    Then I should see "HtmlTemplate"
    Then I should see "TagTree.php"
    Then I should see "No records."
