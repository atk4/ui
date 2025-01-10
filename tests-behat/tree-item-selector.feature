Feature: TreeItemSelector

  Scenario: single
    Given I am on "form-control/tree-item-selector.php"
    When I click using selector "(//div.atk-tree-loader)[2]//div[text()='Cleaner']"
    Then Toast display should contain text "Selected: Cleaner"
    When I click using selector "(//div.atk-tree-loader)[2]//div[text()='Cleaner']"
    Then No toast should be displayed
    When I click using selector "(//div.atk-tree-loader)[2]//i.icon.caret.right[../div/div[text()='Electronics']]"
    Then No toast should be displayed
    When I click using selector "(//div.atk-tree-loader)[2]//div[text()='Phone']"
    Then No toast should be displayed

  Scenario: multiple
    When I click using selector "(//div.atk-tree-loader)[1]//div[text()='Cleaner']"
    Then Toast display should contain text "Appliances"
    When I click using selector "(//div.atk-tree-loader)[1]//div[text()='Cleaner']"
    Then Toast display should contain text "Appliances"
    Then Toast display should contain text "Cleaner"
    When I click using selector "(//div.atk-tree-loader)[1]//i.icon.caret.right[../div/div[text()='Electronics']]"
    Then No toast should be displayed
    When I click using selector "(//div.atk-tree-loader)[1]//div[text()='Phone']"
    Then Toast display should contain text "Appliances"
    Then Toast display should contain text "Electronics > Phone > iPhone"
    When I click using selector "(//div.atk-tree-loader)[1]//div[text()='Phone']"
    Then Toast display should contain text "Appliances"
    When I click using selector "(//div.atk-tree-loader)[1]//div[text()='Electronics']"
    Then Toast display should contain text "Appliances"
    Then Toast display should contain text "Electronics > Tv"
