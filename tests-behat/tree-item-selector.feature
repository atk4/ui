Feature: TreeItemSelector

  Scenario: single
    Given I am on "form-control/tree-item-selector.php"
    Then I click using selector "(//div.atk-tree-loader)[2]//div[text()='Cleaner']"
    Then Toast display should contain text "Selected: Cleaner"
    Then I click using selector "(//div.atk-tree-loader)[2]//div[text()='Cleaner']"
    Then No toast should be displayed
    Then I click using selector "(//div.atk-tree-loader)[2]//i.icon.caret.right[../div/div[text()='Electronics']]"
    Then No toast should be displayed
    Then I click using selector "(//div.atk-tree-loader)[2]//div[text()='Phone']"
    Then No toast should be displayed

  Scenario: multiple
    Then I click using selector "(//div.atk-tree-loader)[1]//div[text()='Cleaner']"
    Then Toast display should contain text "Appliances"
    Then I click using selector "(//div.atk-tree-loader)[1]//div[text()='Cleaner']"
    Then Toast display should contain text "Appliances"
    Then Toast display should contain text "Cleaner"
    Then I click using selector "(//div.atk-tree-loader)[1]//i.icon.caret.right[../div/div[text()='Electronics']]"
    Then No toast should be displayed
    Then I click using selector "(//div.atk-tree-loader)[1]//div[text()='Phone']"
    Then Toast display should contain text "Appliances"
    Then Toast display should contain text "Electronics > Phone > iPhone"
    Then I click using selector "(//div.atk-tree-loader)[1]//div[text()='Phone']"
    Then Toast display should contain text "Appliances"
