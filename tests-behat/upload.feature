Feature: Upload

  Scenario:
    Given I am on "form-control/upload.php"
    When I select file input "file" with "Foo" as "bar.txt"
    Then Toast display should contain text "(name: bar.txt, md5: 1356c67d7ad1638d816bfb822dd2c25d)"

    When I select file input "file" with "Žlutý kůň" as ".$ň"
    Then Toast display should contain text "(name: .$ň, md5: b047fb155be776f5bbae061c7b08cdf0)"
