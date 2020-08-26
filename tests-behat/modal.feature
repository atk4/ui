Feature: Modal
  Testing modal view

  Scenario: Modal
    Given I am on "interactive/modal.php"
    And I press button "Open Lorem Ipsum"
    Then Modal is open with text "Showing lorem ipsum"

  Scenario: ModalExecutor Reload
    Given I am on "_unit-test/modal-reload.php"
    And I press button "Test"
    Then Modal is open with text "Reload success"
