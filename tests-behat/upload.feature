Feature: Upload

  Scenario:
    Given I am on "form-control/upload.php"
    When I select file input "file" with "Foo" as "bar.txt"
    Then Toast display should contain text "(name: bar.txt, md5: 1356c67d7ad1638d816bfb822dd2c25d)"

    When I select file input "file" with "Žlutý kůň" as "$kůň"
    Then Toast display should contain text "(name: $kůň, md5: b047fb155be776f5bbae061c7b08cdf0)"

    When I click using selector "//div.action[.//input[@name='file']]//div.button"
    Then Toast display should contain text "has been removed"

    When I select file input "img" with "Foo" as "bar.png"
    Then Toast display should contain text "is uploaded"

    When I click using selector "//div.action[.//input[@name='img']]//div.button"
    Then Toast display should contain text "has been removed"
    Then Element "//div.action[.//div//input[@name='img']]//img" attribute "src" should contain text "default.png"
