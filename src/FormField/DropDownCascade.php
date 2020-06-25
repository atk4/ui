<?php

declare(strict_types=1);
/**
 * Dropdown form field that will based it's list value
 * according to another input value.
 * Also possible to cascade value from another cascade field.
 * For example:
 *  - you need to narrow product base on Category and sub category
 *       $f = Form::addTo($app);
 *       $f->addElement('category_id', [DropDown::class, 'model' => new Category($db)])->set(3);
 *       $f->addElement('sub_category_id', [DropDownCascade::class, 'cascadeFrom' => 'category_id', 'reference' => 'SubCategories']);
 *       $f->addElement('product_id', [DropDownCascade::class, 'cascadeFrom' => 'sub_category_id', 'reference' => 'Products']);.
 */

namespace atk4\ui\FormField;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Use atk4\ui\Form\Control\DropdownCascade instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed jun-2021
 */
class DropDownCascade extends \atk4\ui\Form\Control\DropdownCascade
{
}
