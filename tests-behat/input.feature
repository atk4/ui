Feature: Input control

  Scenario: Password with reveal eye
    Given I am on "form-control/input2.php"
    When I fill in "password_re_norm" with "Foo secret"
    Then Element "//input[@name='password_re_norm']" attribute "type" should contain text "password"
    Then Element "//input[@name='password_re_norm']/../i[contains(@class, 'eye')]" attribute "class" should contain text "grey eye link slash icon"
    When I click using selector "//input[@name='password_re_norm']/../i[contains(@class, 'eye')]"
    Then Element "//input[@name='password_re_norm']" attribute "type" should contain text "text"
    Then Element "//input[@name='password_re_norm']/../i[contains(@class, 'eye')]" attribute "class" should contain text "eye link icon"
    When I click using selector "//input[@name='password_re_norm']/../i[contains(@class, 'eye')]"
    Then Element "//input[@name='password_re_norm']" attribute "type" should contain text "password"
    Then Element "//input[@name='password_re_norm']/../i[contains(@class, 'eye')]" attribute "class" should contain text "eye link icon slash grey"
