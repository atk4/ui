Feature: Password with revealEye

  Scenario:
    Given I am on "interactive/popup.php"
    When I fill in "password" with "123"
    Then "password" should contain text "***"
    When I press button "//div[@id='atk_layout_maestro_popup_3_view_form_form_layout_password[i.eye.slash.icon]"
    Then "password" should contain text "123"
    When I press button "//div[@id='atk_layout_maestro_popup_3_view_form_form_layout_password[i.eye.icon]"
    Then "password" should contain "***"
