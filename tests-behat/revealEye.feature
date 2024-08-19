Feature: Password with revealEye

  Scenario:
    Given I am on "form-control/input2.php"
    When I fill in "password_re_norm" with "123"
    Then "password_re_norm" should contain text "***"
    When I press button "re_norm_button_icon"
    Then "password_re_norm" should contain text "123"
    When I press button "re_norm_button_icon"
    Then "password_re_norm" should contain text "***"
