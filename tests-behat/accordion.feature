Feature: Accordion

  Scenario: Form Accordion Section is activated
    Given I am on "form/form-section-accordion.php"
    Then I should see "Email"
    When I fill in "email" with "xxx@xxx.com"

  Scenario: Nested Accordion
    Given I am on "interactive/accordion-nested.php"
    When I click using selector "(//div[text()='Static Text'])[1]"
    When I click using selector "(//div[text()='Static Text'])[1]"
    When I click using selector "(//div[text()='Static Text'])[1]"
    When I click using selector "(//div[text()='Static Text'])[2]"
    When I click using selector "(//div[text()='Dynamic Text'])[3]"
    When I click using selector "(//div[text()='Dynamic Text'])[3]"
    When I click using selector "(//div[text()='Dynamic Text'])[3]"
    When I click using selector "(//div[text()='Dynamic Form'])[4]"
    When I click using selector "(//div[text()='Dynamic Form'])[4]"
    When I click using selector "(//div[text()='Dynamic Form'])[4]"
    When I fill in "email" with "xxx@xxx.com"
    When I press button "Save"
    Then I should see "Subscribed xxx@xxx.com to newsletter."
