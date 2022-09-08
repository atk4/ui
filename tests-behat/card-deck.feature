Feature: CardDeck

  Scenario:
    Given I am on "_unit-test/card-deck.php"

  Scenario: add
    Then I press button "Add"
    Then I fill in "atk_fp_country__name" with "Test"
    Then I fill in "atk_fp_country__iso" with "TT"
    Then I fill in "atk_fp_country__iso3" with "TTT"
    Then I fill in "atk_fp_country__numcode" with "123"
    Then I fill in "atk_fp_country__phonecode" with "1"
    Then I press Modal button "Save"
    Then Toast display should contain text "Form Submit"

  Scenario: search
    Then I fill in "atk-vue-search" with "united kingdom"
    Then I should see "United Kingdom"

  Scenario: edit
    Then I press button "Edit"
    Then Modal is open with text "Edit Country"
    Then I press Modal button "Save"
    Then Toast display should contain text "Form Submit"
    # make sure search query stick
    Then I should see "United Kingdom"

  Scenario: delete
    Then I press button "Delete"
    Then I press Modal button "Ok"
    # TODO https://github.com/atk4/ui/issues/1848
    # Then I should not see "United Kingdom"
