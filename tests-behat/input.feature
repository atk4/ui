Feature: Input control

  Scenario: Password reveal
    Given I am on "form-control/input2.php"
    When I fill in "password_norm" with "Foo secret"
    Then Element "//input[@name='password_norm']" attribute "type" should contain text "password"
    Then Element "//input[@name='password_norm']/../i[contains(@class, 'eye')]" attribute "class" should contain text "eye link slash icon"
    When I click using selector "//input[@name='password_norm']/../i[contains(@class, 'eye')]"
    Then Element "//input[@name='password_norm']" attribute "type" should contain text "text"
    Then Element "//input[@name='password_norm']/../i[contains(@class, 'eye')]" attribute "class" should contain text "eye link icon"
    When I click using selector "//input[@name='password_norm']/../i[contains(@class, 'eye')]"
    Then Element "//input[@name='password_norm']" attribute "type" should contain text "password"
    Then Element "//input[@name='password_norm']/../i[contains(@class, 'eye')]" attribute "class" should contain text "eye link icon slash"
