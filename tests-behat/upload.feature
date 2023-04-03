Feature: Upload

  Scenario:
    Given I am on "form-control/upload.php"
    When I select file input "file" with "Foo" as "bar.txt"
    Then Toast display should contain text "(name: bar.txt, md5: 1356c67d7ad1638d816bfb822dd2c25d)"

    When I select file input "file" with "Žlutý kůň" as "$kůň"
    Then Toast display should contain text "(name: $kůň, md5: b047fb155be776f5bbae061c7b08cdf0)"

    When I click using selector "#atk_layout_maestro_form_form_layout_file_button"
    Then Toast display should contain text "has been removed"

    When I select file input "img" with "Foo" as "bar.png"
    Then Toast display should contain text "is uploaded"

    When I click using selector "#atk_layout_maestro_form_form_layout_img_button"
    Then Toast display should contain text "has been removed"
    Then Element "#atk_layout_maestro_form_form_layout_img_view" attribute "src" should contain text "default.png"
