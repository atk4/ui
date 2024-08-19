Feature: Password with revealEye

  Scenario:
    Given I am on "interactive/popup.php"
    When I fill in "Password" with "123"
    Then "Password" should contain text "***"
    When I press button "revealEye"
    Then "Password" should contain text "123"
    When I press button "revealEye"
    Then "Password" should contain "***"
