Feature: Modal

  Scenario: Modal
    Given I am on "interactive/modal.php"
    When I press button "Open Lorem Ipsum"
    Then Modal is open with text "Showing lorem ipsum"

  Scenario: ModalExecutor Reload
    Given I am on "_unit-test/modal-reload.php"
    When I press button "Test"
    Then Modal is open with text "Reload success"

  Scenario: close reloaded modal using dimmer
    Given I am on "data-action/jsactions2.php"
    When I press button "Argument/Preview"
    When I fill Modal field "age" with "5"
    When I press Modal button "Next"
    Then Modal is open with text "You age is: 5"
    When I write "[escape]" into selector "document"
    When I press button "Argument/Preview"
    When I fill Modal field "age" with "6"
    When I press Modal button "Next"
    When I press Modal button "Previous"
    When I press Modal button "Next"
    Then Modal is open with text "You age is: 6"
    When I write "[escape]" into selector "document"
    When I press button "Argument/Preview"
    When I fill Modal field "age" with "7"
    When I press Modal button "Next"
    Then Modal is open with text "You age is: 7"
    When I press Modal button "Argument/Preview"
    Then Toast display should contain text "Success: age = 7"
