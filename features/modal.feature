Feature: Modal
  In order to make correct tests
  As a developper
  I need to be able to test javascript


  Scenario:
    Given I am on "modal2.php"
    And I press button "Open Lorem Ipsum"
    And wait for callback
    Then Modal is open with text "Showing lorem ipsum"