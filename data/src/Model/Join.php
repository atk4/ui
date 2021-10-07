<?php

declare(strict_types=1);

namespace Atk4\Data\Model;

use Atk4\Core\DiContainerTrait;
use Atk4\Core\InitializerTrait;
use Atk4\Core\TrackableTrait;
use Atk4\Data\Exception;
use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Data\Reference;

/**
 * Provides generic functionality for joining data.
 *
 * @method Model getOwner()
 */
class Join
{
    use DiContainerTrait;
    use InitializerTrait {
        init as _init;
    }
    use JoinLinkTrait;
    use TrackableTrait;

    /**
     * Name of the table (or collection) that can be used to retrieve data from.
     * For SQL, This can also be an expression or sub-select.
     *
     * @var string
     */
    protected $foreign_table;

    /**
     * If $persistence is set, then it's used for loading
     * and storing the values, instead $owner->persistence.
     *
     * @var Persistence|Persistence\Sql|null
     */
    protected $persistence;

    /**
     * ID used by a joined table.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Field that is used as native "ID" in the foreign table.
     * When deleting record, this field will be conditioned.
     *
     * ->where($join->id_field, $join->id)->delete();
     *
     * @var string
     */
    protected $id_field = 'id';

    /**
     * By default this will be either "inner" (for strong) or "left" for weak joins.
     * You can specify your own type of join by passing ['kind' => 'right']
     * as second argument to join().
     *
     * @var string
     */
    protected $kind;

    /**
     * Is our join weak? Weak join will stop you from touching foreign table.
     *
     * @var bool
     */
    protected $weak = false;

    /**
     * Normally the foreign table is saved first, then it's ID is used in the
     * primary table. When deleting, the primary table record is deleted first
     * which is followed by the foreign table record.
     *
     * If you are using the following syntax:
     *
     * $user->join('contact','default_contact_id');
     *
     * Then the ID connecting tables is stored in foreign table and the order
     * of saving and delete needs to be reversed. In this case $reverse
     * will be set to `true`. You can specify value of this property.
     *
     * @var bool
     */
    protected $reverse;

    /**
     * Field to be used for matching inside master table. By default
     * it's $foreign_table.'_id'.
     * Note that it should be actual field name in master table.
     *
     * @var string
     */
    protected $master_field;

    /**
     * Field to be used for matching in a foreign table. By default
     * it's 'id'.
     * Note that it should be actual field name in foreign table.
     *
     * @var string
     */
    protected $foreign_field;

    /**
     * A short symbolic name that will be used as an alias for the joined table.
     *
     * @var string
     */
    public $foreign_alias;

    /**
     * When $prefix is set, then all the fields generated through
     * our wrappers will be automatically prefixed inside the model.
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * Data which is populated here as the save/insert progresses.
     *
     * @var array
     */
    protected $save_buffer = [];

    public function __construct(string $foreign_table = null)
    {
        $this->foreign_table = $foreign_table;
    }

    protected function onHookShortToOwner(string $spot, \Closure $fx, array $args = [], int $priority = 5): int
    {
        $name = $this->short_name; // use static function to allow this object to be GCed

        return $this->getOwner()->onHookDynamicShort(
            $spot,
            static function (Model $owner) use ($name) {
                return $owner->getElement($name);
            },
            $fx,
            $args,
            $priority
        );
    }

    /**
     * Will use either foreign_alias or create #join_<table>.
     */
    public function getDesiredName(): string
    {
        return '#join_' . $this->foreign_table;
    }

    /**
     * Initialization.
     */
    protected function init(): void
    {
        $this->_init();

        // owner model should have id_field set
        $id_field = $this->getOwner()->id_field;
        if (!$id_field) {
            throw (new Exception('Joins owner model should have id_field set'))
                ->addMoreInfo('model', $this->getOwner());
        }

        // handle foreign table containing a dot - that will be reverse join
        if (strpos($this->foreign_table, '.') !== false) {
            // split by LAST dot in foreign_table name
            [$this->foreign_table, $this->foreign_field] = preg_split('~\.+(?=[^.]+$)~', $this->foreign_table);

            if (!isset($this->reverse)) {
                $this->reverse = true;
            }
        }

        if ($this->reverse === true) {
            if (isset($this->master_field) && $this->master_field !== $id_field) { // TODO not implemented yet, see https://github.com/atk4/data/issues/803
                throw (new Exception('Joining tables on non-id fields is not implemented yet'))
                    ->addMoreInfo('condition', $this->getOwner()->table . '.' . $this->master_field . ' = ' . $this->foreign_table . '.' . $this->foreign_field);
            }

            if (!$this->master_field) {
                $this->master_field = $id_field;
            }

            if (!$this->foreign_field) {
                $this->foreign_field = $this->getOwner()->table . '_' . $id_field;
            }
        } else {
            $this->reverse = false;

            if (!$this->master_field) {
                $this->master_field = $this->foreign_table . '_' . $id_field;
            }

            if (!$this->foreign_field) {
                $this->foreign_field = $id_field;
            }
        }

        $this->onHookShortToOwner(Model::HOOK_AFTER_UNLOAD, \Closure::fromCallable([$this, 'afterUnload']));
    }

    /**
     * Adding field into join will automatically associate that field
     * with this join. That means it won't be loaded from $table, but
     * form the join instead.
     */
    public function addField(string $name, array $seed = []): Field
    {
        $seed['joinName'] = $this->short_name;

        return $this->getOwner()->addField($this->prefix . $name, $seed);
    }

    /**
     * Adds multiple fields.
     *
     * @return $this
     */
    public function addFields(array $fields = [])
    {
        foreach ($fields as $field) {
            if (is_array($field)) {
                $name = $field[0];
                unset($field[0]);
                $this->addField($name, $field);
            } else {
                $this->addField($field);
            }
        }

        return $this;
    }

    /**
     * Another join will be attached to a current join.
     *
     * @return self
     */
    public function join(string $foreign_table, array $defaults = [])
    {
        $defaults['joinName'] = $this->short_name;

        return $this->getOwner()->join($foreign_table, $defaults);
    }

    /**
     * Another leftJoin will be attached to a current join.
     *
     * @return self
     */
    public function leftJoin(string $foreign_table, array $defaults = [])
    {
        $defaults['joinName'] = $this->short_name;

        return $this->getOwner()->leftJoin($foreign_table, $defaults);
    }

    /**
     * weakJoin will be attached to a current join.
     *
     * @todo NOT IMPLEMENTED! weakJoin method does not exist!
     *
     * @return
     */
    /*
    public function weakJoin($defaults = [])
    {
        $defaults['joinName'] = $this->short_name;

        return $this->getOwner()->weakJoin($defaults);
    }
    */

    /**
     * Creates reference based on a field from the join.
     *
     * @return Reference\HasOne
     */
    public function hasOne(string $link, array $defaults = [])
    {
        $defaults['joinName'] = $this->short_name;

        return $this->getOwner()->hasOne($link, $defaults);
    }

    /**
     * Creates reference based on the field from the join.
     *
     * @return Reference\HasMany
     */
    public function hasMany(string $link, array $defaults = [])
    {
        $defaults = array_merge([
            'our_field' => $this->id_field,
            'their_field' => $this->getOwner()->table . '_' . $this->id_field,
        ], $defaults);

        return $this->getOwner()->hasMany($link, $defaults);
    }

    /**
     * Wrapper for containsOne that will associate field
     * with join.
     *
     * @todo NOT IMPLEMENTED !
     *
     * @return ???
     */
    /*
    public function containsOne(Model $model, array $defaults = [])
    {
        if (is_string($defaults[0])) {
            $defaults[0] = $this->addField($defaults[0], ['system' => true]);
        }

        return parent::containsOne($model, $defaults);
    }
    */

    /**
     * Wrapper for containsMany that will associate field
     * with join.
     *
     * @todo NOT IMPLEMENTED !
     *
     * @return ???
     */
    /*
    public function containsMany(Model $model, array $defaults = [])
    {
        if (is_string($defaults[0])) {
            $defaults[0] = $this->addField($defaults[0], ['system' => true]);
        }

        return parent::containsMany($model, $defaults);
    }
    */

    /**
     * Will iterate through this model by pulling
     *  - fields
     *  - references
     *  - conditions.
     *
     * and then will apply them locally. If you think that any fields
     * could clash, then use ['prefix' => 'm2'] which will be pre-pended
     * to all the fields. Conditions will be automatically mapped.
     *
     * @todo NOT IMPLEMENTED !
     */
    /*
    public function importModel(Model $model, array $defaults = [])
    {
        // not implemented yet !!!
    }
    */

    /**
     * Joins with the primary table of the model and
     * then import all of the data into our model.
     *
     * @todo NOT IMPLEMENTED!
     */
    /*
    public function weakJoinModel(Model $model, array $fields = [])
    {
        if (!is_object($model)) {
            $model = $this->getOwner()->connection->add($model);
        }
        $j = $this->join($model->table);

        $j->importModel($model);

        return $j;
    }
    */

    /**
     * Set value.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($field, $value)
    {
        $this->save_buffer[$field] = $value;

        return $this;
    }

    /**
     * Clears id and save buffer.
     */
    protected function afterUnload(): void
    {
        $this->id = null;
        $this->save_buffer = [];
    }
}
