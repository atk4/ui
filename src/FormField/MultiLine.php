<?php

declare(strict_types=1);
/**
 * Creates a Multiline field within a table, which allows adding/editing multiple
 * data rows.
 *
 * To save the data, use the Multiline::saveRows() method. If the Multiline's
 * model is a reference to your form's model, the form model should be saved prior
 * to calling saveRows().
 *
 * $f = \atk4\ui\Form::addTo($app);
 * $f->setModel($invoice, false);
 * // Add Form fields
 *
 * // Add Multiline field and set model for it.
 * $ml = $f->addField('ml', ['Multiline']);
 *
 * // Set model using hasMany reference of Invoice.
 * $ml->setModel($invoice, ['item','cat','qty','price', 'total'], 'Items', 'invoice_id');
 *
 * $f->onSubmit(function($f) use ($ml) {
 *     // Save Form model and then Multiline model
 *     $f->model->save();
 *     $ml->saveRows();
 *     return new \atk4\ui\jsToast('Saved!');
 * });
 *
 * If Multiline's model contains expressions, these will be evaluated on the fly
 * whenever data gets entered.
 *
 * Multiline input also has an onChange callback that will return all data rows
 * in an array. It is also possible to fire onChange handler only for certain
 * fields by passing them as an array to the method.
 *
 * Note that deleting a row will always fire the onChange callback.
 *
 * You can use the returned data to update other related areas of the form.
 * For example, ypdating Grand Total field of all invoice items.
 *
 * $ml->onChange(function($rows) use ($f) {
 *     $grand_total = 0;
 *     foreach ($rows as $row => $cols) {
 *         foreach ($cols as $col) {
 *             $fieldName = key($col);
 *                 if ($fieldName === 'total') {
 *                     $grand_total = $grand_total + $col[$fieldName];
 *                 }
 *          }
 *     }
 *
 *   return $f->js(true, null, 'input[name="grand_total"]')->val(number_format($grand_total, 2));
 * }, ['qty', 'price']);
 *
 * Finally, it's also possible to use Multiline for quickly adding records to a
 * model. Be aware that in the example below all User records will be displayed.
 * If your model contains a lot of records, you should handle their limit somehow.
 *
 * $f = \atk4\ui\Form::addTo($app);
 * $ml = $f->addField('ml', [\atk4\ui\FormField\MultiLine::class]);
 * $ml->setModel($user, ['name','is_vip']);
 *
 * $f->onSubmit(function($f) use ($ml) {
 *     $ml->saveRows();
 *     return new \atk4\ui\jsToast('Saved!');
 * });
 */

namespace atk4\ui\FormField;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Use atk4\ui\Form\Field\Multiline instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed jun-2021
 */
class MultiLine extends \atk4\ui\Form\Field\Multiline
{
}
