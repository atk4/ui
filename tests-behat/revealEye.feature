Feature: Password with revealEye

  Scenario:
    Given I am on "form-control/input2.php"
    When I fill in "password_re_norm" with "123"
    Then Element "//input[@name='password_re_norm']/following-sibling::i[contains(@class, 'eye')]" attribute "class" should contain text "grey eye link slash icon"
    When I click using selector "//input[@name='password_re_norm']/following-sibling::i[contains(@class, 'eye')]"
    Then Element "//input[@name='password_re_norm']/following-sibling::i[contains(@class, 'eye')]" attribute "class" should contain text "grey eye link icon"

