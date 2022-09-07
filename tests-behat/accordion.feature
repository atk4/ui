Feature: Accordion

  Scenario: Form Accordion Section is activated
    Given I am on "form/form-section-accordion.php"
    Then I should see "Email"
    Then I fill in "email" with "xxx@xxx.com"
