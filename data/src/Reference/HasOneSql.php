<?php

declare(strict_types=1);

namespace Atk4\Data\Reference;

use Atk4\Data\Exception;
use Atk4\Data\Field;
use Atk4\Data\FieldSqlExpression;
use Atk4\Data\Model;
use Atk4\Data\Persistence;

class HasOneSql extends HasOne
{
    /**
     * Creates expression which sub-selects a field inside related model.
     *
     * Returns Expression in case you want to do something else with it.
     *
     * @param string|Field|array $ourFieldName or [$field, ..defaults]
     */
    public function addField($ourFieldName, string $theirFieldName = null): FieldSqlExpression
    {
        if (is_array($ourFieldName)) {
            $defaults = $ourFieldName;
            if (!isset($defaults[0])) {
                throw (new Exception('Field name must be specified'))
                    ->addMoreInfo('field', $ourFieldName);
            }
            $ourFieldName = $defaults[0];
            unset($defaults[0]);
        } else {
            $defaults = [];
        }

        if ($theirFieldName === null) {
            $theirFieldName = $ourFieldName;
        }

        $ourModel = $this->getOurModel();

        // if caption/type is not defined in $defaults -> get it directly from the linked model field $theirFieldName
        $defaults['caption'] ??= $ourModel->refModel($this->link)->getField($theirFieldName)->getCaption();
        $defaults['type'] ??= $ourModel->refModel($this->link)->getField($theirFieldName)->type;

        /** @var FieldSqlExpression $fieldExpression */
        $fieldExpression = $ourModel->addExpression($ourFieldName, array_merge(
            [
                function (Model $ourModel) use ($theirFieldName) {
                    // remove order if we just select one field from hasOne model
                    // that is mandatory for Oracle
                    return $ourModel->refLink($this->link)->action('field', [$theirFieldName])->reset('order');
                },
            ],
            $defaults,
            [
                // to be able to change field, but not save it
                // afterSave hook will take care of the rest
                'read_only' => false,
                'never_save' => true,
            ]
        ));

        // Will try to execute last
        $this->onHookToOurModel($ourModel, Model::HOOK_BEFORE_SAVE, function (Model $ourModel) use ($ourFieldName, $theirFieldName) {
            // if title field is changed, but reference ID field (our_field)
            // is not changed, then update reference ID field value
            if ($ourModel->isDirty($ourFieldName) && !$ourModel->isDirty($this->our_field)) {
                $theirModel = $this->createTheirModel();

                $theirModel->addCondition($theirFieldName, $ourModel->get($ourFieldName));
                $ourModel->set($this->getOurFieldName(), $theirModel->action('field', [$theirModel->id_field]));
                $ourModel->_unset($ourFieldName);
            }
        }, [], 21);

        return $fieldExpression;
    }

    /**
     * Add multiple expressions by calling addField several times. Fields
     * may contain 3 types of elements:.
     *
     * [ 'name', 'surname' ] - will import those fields as-is
     * [ 'full_name' => 'name', 'day_of_birth' => ['dob', 'type' => 'date'] ] - use alias and options
     * [ ['dob', 'type' => 'date'] ]  - use options
     *
     * You may also use second param to specify parameters:
     *
     * addFields(['from', 'to'], ['type' => 'date']);
     *
     * @return $this
     */
    public function addFields(array $fields = [], array $defaults = [])
    {
        foreach ($fields as $ourFieldName => $ourFieldDefaults) {
            $ourFieldDefaults = array_merge($defaults, (array) $ourFieldDefaults);

            if (!isset($ourFieldDefaults[0])) {
                throw (new Exception('Incorrect definition for addFields. Field name must be specified'))
                    ->addMoreInfo('ourFieldName', $ourFieldName)
                    ->addMoreInfo('ourFieldDefaults', $ourFieldDefaults);
            }

            $theirFieldName = $ourFieldDefaults[0];

            if (is_int($ourFieldName)) {
                $ourFieldName = $theirFieldName;
            }

            $ourFieldDefaults[0] = $ourFieldName;

            $this->addField($ourFieldDefaults, $theirFieldName);
        }

        return $this;
    }

    /**
     * Creates model that can be used for generating sub-query actions.
     */
    public function refLink(array $defaults = []): Model
    {
        $theirModel = $this->createTheirModel($defaults);

        $theirModel->addCondition(
            $this->their_field ?: $theirModel->id_field,
            $this->referenceOurValue()
        );

        return $theirModel;
    }

    /**
     * Navigate to referenced model.
     */
    public function ref(array $defaults = []): Model
    {
        $theirModel = parent::ref($defaults);
        $ourModel = $this->getOurModel();

        if (!isset($ourModel->persistence) || !($ourModel->persistence instanceof Persistence\Sql)) {
            return $theirModel;
        }

        $theirField = $this->their_field ?: $theirModel->id_field;
        $ourField = $this->getOurField();

        // At this point the reference
        // if our_field is the id_field and is being used in the reference
        // we should persist the relation in condtition
        // example - $model->load(1)->ref('refLink')->import($rows);
        if ($ourModel->isEntity() && $ourModel->loaded() && !$theirModel->loaded()) {
            if ($ourModel->id_field === $this->getOurFieldName()) {
                return $theirModel->getModel()
                    ->addCondition($theirField, $this->getOurFieldValue());
            }
        }

        // handles the deep traversal using an expression
        $ourFieldExpression = $ourModel->action('field', [$ourField]);

        $theirModel->getModel(true)
            ->addCondition($theirField, $ourFieldExpression);

        return $theirModel;
    }

    /**
     * Add a title of related entity as expression to our field.
     *
     * $order->hasOne('user_id', 'User')->addTitle();
     *
     * This will add expression 'user' equal to ref('user_id')['name'];
     *
     * This method returns newly created expression field.
     */
    public function addTitle(array $defaults = []): FieldSqlExpression
    {
        $ourModel = $this->getOurModel();

        $fieldName = $defaults['field'] ?? preg_replace('~_(' . preg_quote($ourModel->id_field, '~') . '|id)$~', '', $this->link);

        if ($ourModel->hasField($fieldName)) {
            throw (new Exception('Field with this name already exists. Please set title field name manually addTitle([\'field\' => \'field_name\'])'))
                ->addMoreInfo('field', $fieldName);
        }

        /** @var FieldSqlExpression $fieldExpression */
        $fieldExpression = $ourModel->addExpression($fieldName, array_replace_recursive(
            [
                function (Model $ourModel) {
                    $theirModel = $ourModel->refLink($this->link);

                    return $theirModel->action('field', [$theirModel->title_field])->reset('order');
                },
                'type' => null,
                'ui' => ['editable' => false, 'visible' => true],
            ],
            $defaults,
            [
                // to be able to change title field, but not save it
                // afterSave hook will take care of the rest
                'read_only' => false,
                'never_save' => true,
            ]
        ));

        // Will try to execute last
        $this->onHookToOurModel($ourModel, Model::HOOK_BEFORE_SAVE, function (Model $ourModel) use ($fieldName) {
            // if title field is changed, but reference ID field (our_field)
            // is not changed, then update reference ID field value
            if ($ourModel->isDirty($fieldName) && !$ourModel->isDirty($this->our_field)) {
                $theirModel = $this->createTheirModel();

                $theirModel->addCondition($theirModel->title_field, $ourModel->get($fieldName));
                $ourModel->set($this->getOurFieldName(), $theirModel->action('field', [$theirModel->id_field]));
            }
        }, [], 20);

        // Set ID field as not visible in grid by default
        if (!array_key_exists('visible', $this->getOurField()->ui)) {
            $this->getOurField()->ui['visible'] = false;
        }

        return $fieldExpression;
    }

    /**
     * Add a title of related entity as expression to our field.
     *
     * $order->hasOne('user_id', 'User')->addTitle();
     *
     * This will add expression 'user' equal to ref('user_id')['name'];
     *
     * @return $this
     */
    public function withTitle(array $defaults = [])
    {
        $this->addTitle($defaults);

        return $this;
    }
}
