Feature: Loader

  Scenario:
    Given I am on "interactive/loader2.php"
    When I click using selector "//td[text()='American Samoa']"
    Then I check if input value for "input[name='atk_fp_country__iso3']" match text "ASM"
    When I click using selector "//td[text()='Argentina']"
    Then I check if input value for "input[name='atk_fp_country__iso3']" match text "ARG"
