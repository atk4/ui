Feature: CardDeck

  Scenario:
    Given I am on "_unit-test/card-deck.php"

  Scenario: add
    Then I press button "Add Country"
    Then I fill in "atk_fp_country__name" with "Test"
    Then I fill in "atk_fp_country__iso" with "TT"
    Then I fill in "atk_fp_country__iso3" with "TTT"
    Then I fill in "atk_fp_country__numcode" with "123"
    Then I fill in "atk_fp_country__phonecode" with "1"
    Then I press Modal button "Save"
    Then Toast display should contain text 'Country action "add" with "Test" entity was executed.'

  Scenario: search
    Then I fill in "atk-vue-search" with "united kingdom"
    Then I should see "United Kingdom"

  Scenario: edit
    Then I press button "Edit"
    Then Modal is open with text "Edit Country"
    Then I press Modal button "Save"
    Then Toast display should contain text 'Country action "edit" with "United Kingdom" entity was executed.'
    # make sure search query stick
    Then I should see "United Kingdom"

  Scenario: delete
    Then I press button "Delete"
    Then I press Modal button "Ok"
    Then Toast display should contain text 'Country action "delete" with "United Kingdom" entity was executed.'

  Scenario: delete - with unlocked DB
    When I persist DB changes across requests
    Then I press button "Delete"
    Then I press Modal button "Ok"
    Then Toast display should contain text 'Record has been deleted!'
    Then I should not see "United Kingdom"
