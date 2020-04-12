Feature: UserAction
  In order to make correct tests
  As a developper
  I need to be able to test javascript

  Scenario:
    Given I am on "jsactions2.php"
    And I press button "Callback"
    And wait for callback
    Then Toast display should contains text "callback execute using country"

  Scenario:
    Given I am on "jsactions2.php"
    And I press button "Argument"
    And wait for callback
    Then Modal is showing text "Age" inside tag "label"
    When I fill in "age" with "22"
    Then I press button "Edit Argument"
    And wait for callback
    Then Toast display should contains text "22 is old enough to visit"