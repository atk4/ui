<?php

declare(strict_types=1);
/**
 * Display items in a hierarchical (tree) view structure.
 *
 * When an item contains nodes with non empty values, it will automatically be treat as a group level;
 *
 * The input value is store as an array type when allowMultiple is set to true, otherwise, will
 * store one single value when set to false.
 *
 * Only item id are store within the input field.
 *
 * see demos/tree-item-selector.php to see how tree items are build.
 */

namespace atk4\ui\FormField;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Use atk4\ui\Form\Field\TreeItemSelector instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed jun-2021
 */
class TreeItemSelector extends \atk4\ui\Form\Field\TreeItemSelector
{
}
