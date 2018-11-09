<?php

namespace atk4\ui\FormLayout;

use atk4\ui\Exception;

/**
 * Other atk4/ui/View can be added to this form layout.
 * Each View added can also contains atk4/ui/FormField.
 *
 * Ex Using an accordion
 * Using accordion, you can split your form input into different section of the accordion.
 *
 * //This line will add an accordion view to your form layout.
 * $acc = $f->layout->addView(['Accordion', 'type' => ['styled', 'fluid']]);
 *
 * //This line will enable an accordion section view
 * $acc_section = $f->layout->addLayoutSection($acc->addItem('Contact'));
 *
 * // then addfield to this new layout section.
 * $acc_section->addField('Name');
 *
 * Ex: Splitting form field into columns
 *
 * $cols = $f->layout->addView('Columns');
 * $col_1_layout = $f->layout->addLayoutSection($cols->addColumn());
 * $col_1_layout->addField('first_name');
 * $col_2_layout = $f->layout->addLayoutSection($cols->addColumn());
 * $col_2_layout->addField('last_name');
 */
class Flexible extends Generic
{
    /**
     * Add a View into the form layout.
     * The newly added view or a sub view of the newly added view
     * can later be used to add form field to it.
     *
     * @param string|array|View $view
     * @param bool              $hasDivider
     *
     * @throws \atk4\ui\Exception
     *
     * @return View
     */
    public function addView($view, $hasDivider = true)
    {
        $v = $this->add($view);
        if ($hasDivider) {
            $this->add(['ui' => 'hidden divider']);
        }

        return $v;
    }

    /**
     * Add a form layout section into your view where you can add form field to it.
     * This is done by adding a FormLayout into the view.
     * This method return a FormLayout where field can be added.
     *
     * @param View   $view   The layout view where field needs to be added.
     * @param string $layout The layout used to added field in view.
     *
     * @throws Exception
     *
     * @return mixed The form layout where field can be added.
     */
    public function addLayoutSection($view, $layout = 'FormLayout/Generic')
    {
        if (!$this->form) {
            throw new Exception('There is no form attached to this layout');
        }

        return $view->add([$layout, 'form' => $this->form]);
    }
}
