Feature: Nested modals /w error handling

  Scenario: Modal with PHP error exception is displayed
    Given I am on "_unit-test/modal-error.php"
    When I press button "Test"
    When I press button "Test Modal load PHP error"
    Then Modal is open with text "Error: Exception from Modal"
    Then I hide js modal
    When I press button "Test Modal load PHP error"
    Then Modal is open with text "Error: Exception from Modal"
    Then I hide js modal

  Scenario: Modal with JS error
    When I press button "Test Modal load JS error"
    Then Modal is open with text "Javascript Error"
    Then Modal is open with text 'Fomantic-UI "modal.onShow" setting cannot be customized outside atk'
    Then I hide js modal
    When I press button "Test Modal load JS error"
    Then Modal is open with text 'Fomantic-UI "modal.onShow" setting cannot be customized outside atk'
    Then I hide js modal

  Scenario: ModalExecutor with PHP error exception is displayed
    When I press button "Test ModalExecutor load PHP error"
    Then Modal is open with text "Atk4\Data\Exception: Record with specified ID was not found"
    Then I hide js modal
    When I press button "Test ModalExecutor load PHP error"
    Then Modal is open with text "Atk4\Data\Exception: Record with specified ID was not found"
    Then I hide js modal
