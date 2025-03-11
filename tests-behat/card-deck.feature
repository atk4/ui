Feature: CardDeck

  Scenario:
    Given I am on "_unit-test/card-deck.php"

  Scenario: add
    When I press button "Add Country"
    When I fill in "atk_fp_country__name" with "Test"
    When I fill in "atk_fp_country__iso" with "XT"
    When I fill in "atk_fp_country__iso3" with "XTT"
    When I fill in "atk_fp_country__numcode" with "123"
    When I fill in "atk_fp_country__phonecode" with "1"
    When I press Modal button "Save"
    Then Toast display should contain text "Country action \"add\" with \"Test\" entity was executed."

  Scenario: search
    When I fill in "atk-vue-search" with "united kingdom"
    Then I should see "United Kingdom"

  Scenario: edit
    When I press button "Edit"
    Then Modal is open with text "Edit Country"
    When I press Modal button "Save"
    Then Toast display should contain text "Country action \"edit\" with \"United Kingdom\" entity was executed."
    # make sure search query stick
    Then I should see "United Kingdom"

  Scenario: delete
    When I press button "Delete"
    When I press Modal button "Ok"
    Then Toast display should contain text "Country action \"delete\" with \"United Kingdom\" entity was executed."

  Scenario: delete - with unlocked DB
    When I persist DB changes across requests
    When I press button "Delete"
    When I press Modal button "Ok"
    Then Toast display should contain text "Record has been deleted!"
    Then I should not see "United Kingdom"
