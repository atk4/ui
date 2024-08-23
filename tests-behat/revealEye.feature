Feature: Password with revealEye

  Scenario:
    Given I am on "form-control/input2.php"
    When I fill in "password_re_norm" with "123"
    Then Element "input[name='password_re_norm']~i.grey.eye" attribute "class" should contain text "slash"
    When I press button "re_read_button_icon"
    Then Element "input[name='password_re_norm']~i.grey.eye" attribute "class" should contain text "grey eye link icon"

