Feature: Vue

  Scenario: testing InlineEdit - /w autoSave
    Given I am on "javascript/vue-component.php"
    When I persist DB changes across requests
    When I fill field using "(//input[@name='atk_fp_country__name'])[1]" with "test autoSave"
    Then Toast display should contain text "Update saved"
    Given I am on "javascript/vue-component.php"
    Then I check if input value for "(//input[@name='atk_fp_country__name'])[1]" match text "test autoSave"
    When I fill field using "(//input[@name='atk_fp_country__name'])[1]" with "germany"
    Then Toast display should contain text "Country name must be unique."
    Then I check if input value for "(//input[@name='atk_fp_country__name'])[1]" match text "germany"
    When I write "[escape]" into selector "(//input[@name='atk_fp_country__name'])[1]"
    Then I check if input value for "(//input[@name='atk_fp_country__name'])[1]" match text "test autoSave"

  Scenario: testing InlineEdit - /w onChange callback
    Given I am on "javascript/vue-component.php"
    When I fill field using "(//input[@name='atk_fp_country__name'])[2]" with "test callback"
    Then I should see "new value: test callback"
    Then I hide js modal

  Scenario: testing ItemSearch
    When I fill in "atk-vue-search" with "united kingdom"
    Then I should see "United Kingdom"
