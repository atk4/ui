Feature: Modal

  Scenario: Modal
    Given I am on "interactive/modal.php"
    When I press button "Open Lorem Ipsum"
    Then Modal is open with text "Showing lorem ipsum"

  Scenario: ModalExecutor Reload
    Given I am on "_unit-test/modal-reload.php"
    When I press button "Test"
    Then Modal is open with text "Reload success"
