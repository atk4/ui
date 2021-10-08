<?php

declare(strict_types=1);

namespace Atk4\Data;

use Atk4\Core\DiContainerTrait;
use Atk4\Core\ReadableCaptionTrait;
use Atk4\Core\TrackableTrait;
use Atk4\Data\Model\Scope;
use Atk4\Data\Persistence\Sql\Expression;
use Atk4\Data\Persistence\Sql\Expressionable;
use Doctrine\DBAL\Types\Type;

/**
 * @method Model getOwner()
 */
class Field implements Expressionable
{
    use DiContainerTrait {
        setDefaults as _setDefaults;
    }
    use Model\FieldPropertiesTrait;
    use Model\JoinLinkTrait;
    use ReadableCaptionTrait;
    use TrackableTrait;

    // {{{ Core functionality

    /**
     * Constructor. You can pass field properties as array.
     */
    public function __construct(array $defaults = [])
    {
        foreach ($defaults as $key => $val) {
            if (is_array($val)) {
                $this->{$key} = array_replace_recursive(is_array($this->{$key} ?? null) ? $this->{$key} : [], $val);
            } else {
                $this->{$key} = $val;
            }
        }
    }

    public function setDefaults(array $properties, bool $passively = false): self
    {
        $this->_setDefaults($properties, $passively);

        $this->getTypeObject(); // assert type exists

        return $this;
    }

    public function getTypeObject(): Type
    {
        if ($this->type === 'array') { // remove in 2022-mar
            throw new Exception('Atk4 "array" type is no longer supported, originally, it serialized value to JSON, to keep this behaviour, use "json" type');
        }

        return Type::getType($this->type ?? 'string');
    }

    protected function onHookShortToOwner(string $spot, \Closure $fx, array $args = [], int $priority = 5): int
    {
        $name = $this->short_name; // use static function to allow this object to be GCed

        return $this->getOwner()->onHookDynamicShort(
            $spot,
            static function (Model $owner) use ($name) {
                return $owner->getField($name);
            },
            $fx,
            $args,
            $priority
        );
    }

    /**
     * Depending on the type of a current field, this will perform
     * some normalization for strict types. This method must also make
     * sure that $f->required is respected when setting the value, e.g.
     * you can't set value to '' if type=string and required=true.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function normalize($value)
    {
        $this->getTypeObject(); // assert type exists

        try {
            if ($this->getOwner()->hook(Model::HOOK_NORMALIZE, [$this, $value]) === false) {
                return $value;
            }

            if (is_string($value)) {
                switch ($this->type) {
                    case null:
                    case 'string':
                        $value = trim(str_replace(["\r", "\n"], '', $value)); // remove all line-ends and trim

                        break;
                    case 'text':
                        $value = trim(str_replace(["\r\n", "\r"], "\n", $value)); // normalize line-ends to LF and trim

                        break;
                    case 'boolean':
                    case 'integer':
                        $value = preg_replace('/\s+|[,`\']/', '', $value);

                        break;
                    case 'float':
                    case 'atk4_money':
                        $value = preg_replace('/\s+|[,`\'](?=.*\.)/', '', $value);

                        break;
                }

                switch ($this->type) {
                    case 'boolean':
                    case 'integer':
                    case 'float':
                    case 'atk4_money':
                        if ($value === '') {
                            $value = null;
                        } elseif (!is_numeric($value)) {
                            throw new Exception('Must be numeric');
                        }

                        break;
                }
            } elseif ($value !== null) {
                switch ($this->type) {
                    case null:
                    case 'string':
                    case 'text':
                    case 'integer':
                    case 'float':
                    case 'atk4_money':
                        if (is_bool($value)) {
                            if ($this->type === 'boolean') {
                                $value = $value ? '1' : '0';
                            } else {
                                throw new Exception('Must not be boolean type');
                            }
                        } elseif (is_scalar($value)) {
                            $value = (string) $value;
                        } else {
                            throw new Exception('Must be scalar');
                        }

                        break;
                }
            }

            // normalize using persistence typecasting
            $persistence = $this->getOwner()->persistence
                ?? new class() extends Persistence {
                    public function __construct()
                    {
                    }
                };
            $value = $persistence->typecastSaveField($this, $value);
            $value = $persistence->typecastLoadField($this, $value);

            if ($value === null) {
                if ($this->required/* known bug, see https://github.com/atk4/data/issues/575, fix in https://github.com/atk4/data/issues/576 || $this->mandatory*/) {
                    throw new Exception('Must not be null');
                }

                return null;
            }

            if ($value === '' && $this->required) {
                throw new Exception('Must not be empty');
            }

            switch ($this->type) {
                case null:
                case 'string':
                case 'text':
                    if ($this->required && empty($value)) {
                        throw new Exception('Must not be empty');
                    }

                    break;
                case 'boolean':
                    if ($this->required && empty($value)) {
                        throw new Exception('Must be true');
                    }

                    break;
                case 'integer':
                case 'float':
                case 'atk4_money':
                    if ($this->required && empty($value)) {
                        throw new Exception('Must not be a zero');
                    }

                    break;
                case 'date':
                case 'datetime':
                case 'time':
                    if (!$value instanceof \DateTimeInterface) {
                        throw new Exception('Must be an instance of DateTimeInterface');
                    }

                    break;
                case 'json':
                    if (!is_array($value)) {
                        throw new Exception('Must be an array');
                    }

                    break;
                case 'object':
                    if (!is_object($value)) {
                        throw new Exception('Must be an object');
                    }

                    break;
            }

            if ($this->enum) {
                if ($value === null || $value === '') {
                    $value = null;
                } elseif (!in_array($value, $this->enum, true)) {
                    throw new Exception('Value is not one of the allowed values: ' . implode(', ', $this->enum));
                }
            }

            if ($this->values) {
                if ($value === null || $value === '') {
                    $value = null;
                } elseif ((!is_string($value) && !is_int($value)) || !array_key_exists($value, $this->values)) {
                    throw new Exception('Value is not one of the allowed values: ' . implode(', ', array_keys($this->values)));
                }
            }

            return $value;
        } catch (\Exception $e) {
            $messages = [];
            do {
                $messages[] = $e->getMessage();
            } while ($e = $e->getPrevious());

            throw (new ValidationException([$this->name => implode(': ', $messages)], $this->getOwner()))
                ->addMoreInfo('field', $this);
        }
    }

    /**
     * Casts field value to string.
     *
     * @param mixed $value Optional value
     */
    public function toString($value = null): string
    {
        $value = ($value === null /* why not func_num_args() === 1 */ ? $this->get() : $this->normalize($value));
        if (is_bool($value)) {
            $value = $value ? '1' : '0';
        }

        return (string) $this->typecastSaveField($value, true);
    }

    /**
     * Returns field value.
     *
     * @return mixed
     */
    public function get()
    {
        return $this->getOwner()->get($this->short_name);
    }

    /**
     * Sets field value.
     *
     * @param mixed $value
     */
    public function set($value): self
    {
        $this->getOwner()->set($this->short_name, $value);

        return $this;
    }

    /**
     * Unset field value even if null value is not allowed.
     */
    public function setNull(): self
    {
        $this->getOwner()->setNull($this->short_name);

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function typecastSaveField($value, bool $allowGenericPersistence = false)
    {
        $persistence = $this->getOwner()->persistence;
        if ($persistence === null) {
            if ($allowGenericPersistence) {
                $persistence = new class() extends Persistence {
                    public function __construct()
                    {
                    }
                };
            } else {
                $this->getOwner()->checkPersistence();
            }
        }

        return $persistence->typecastSaveField($this, $value);
    }

    /**
     * @param mixed|void $value
     */
    private function getValueForCompare($value): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string) $this->typecastSaveField($value, true);
    }

    /**
     * Compare new value of the field with existing one without retrieving.
     * In the trivial case it's same as ($value == $model->get($name)) but this method can be used for:
     *  - comparing values that can't be received - passwords, encrypted data
     *  - comparing images
     *  - if get() is expensive (e.g. retrieve object).
     *
     * @param mixed      $value
     * @param mixed|void $value2
     */
    public function compare($value, $value2 = null): bool
    {
        if (func_num_args() === 1) {
            $value2 = $this->get();
        }

        // TODO, see https://stackoverflow.com/questions/48382457/mysql-json-column-change-array-order-after-saving
        // at least MySQL sorts the JSON keys if stored natively
        return $this->getValueForCompare($value) === $this->getValueForCompare($value2);
    }

    public function getReference(): ?Reference
    {
        return $this->referenceLink !== null
            ? $this->getOwner()->getRef($this->referenceLink)
            : null;
    }

    public function getPersistenceName(): string
    {
        return $this->actual ?? $this->short_name;
    }

    /**
     * Should this field use alias?
     */
    public function useAlias(): bool
    {
        return isset($this->actual);
    }

    // }}}

    // {{{ Scope condition

    /**
     * Returns arguments to be used for query on this field based on the condition.
     *
     * @param string|null $operator one of Scope\Condition operators
     * @param mixed       $value    the condition value to be handled
     */
    public function getQueryArguments($operator, $value): array
    {
        $typecastField = $this;
        $allowArray = true;
        if (in_array($operator, [
            Scope\Condition::OPERATOR_LIKE,
            Scope\Condition::OPERATOR_NOT_LIKE,
            Scope\Condition::OPERATOR_REGEXP,
            Scope\Condition::OPERATOR_NOT_REGEXP,
        ], true)) {
            $typecastField = new self(['type' => 'string']);
            $typecastField->setOwner(new Model($this->getOwner()->persistence, ['table' => false]));
            $typecastField->short_name = $this->short_name;
            $allowArray = false;
        }

        if ($value instanceof Persistence\Array_\Action) { // needed to pass hintable tests
            $v = $value;
        } elseif (is_array($value) && $allowArray) {
            $v = array_map(fn ($value) => $typecastField->typecastSaveField($value), $value);
        } else {
            $v = $typecastField->typecastSaveField($value);
        }

        return [$this, $operator, $v];
    }

    // }}}

    // {{{ Handy methods used by UI

    /**
     * Returns if field should be editable in UI.
     */
    public function isEditable(): bool
    {
        return $this->ui['editable'] ?? !$this->read_only && !$this->never_persist && !$this->system;
    }

    /**
     * Returns if field should be visible in UI.
     */
    public function isVisible(): bool
    {
        return $this->ui['visible'] ?? !$this->system;
    }

    /**
     * Returns if field should be hidden in UI.
     */
    public function isHidden(): bool
    {
        return $this->ui['hidden'] ?? false;
    }

    /**
     * Returns field caption for use in UI.
     */
    public function getCaption(): string
    {
        return $this->caption ?? $this->ui['caption'] ?? $this->readableCaption($this->short_name);
    }

    // }}}

    /**
     * When field is used as expression, this method will be called.
     * Universal way to convert ourselves to expression. Off-load implementation into persistence.
     */
    public function getDsqlExpression(Expression $expression): Expression
    {
        if (!$this->getOwner()->persistence || !$this->getOwner()->persistence instanceof Persistence\Sql) {
            throw (new Exception('Field must have SQL persistence if it is used as part of expression'))
                ->addMoreInfo('persistence', $this->getOwner()->persistence ?? null);
        }

        return $this->getOwner()->persistence->getFieldSqlExpression($this, $expression);
    }

    // {{{ Debug Methods

    /**
     * Returns array with useful debug info for var_dump.
     */
    public function __debugInfo(): array
    {
        $arr = [
            'short_name' => $this->short_name,
            'value' => $this->get(),
        ];

        foreach ([
            'type', 'system', 'never_persist', 'never_save', 'read_only', 'ui', 'joinName',
        ] as $key) {
            if (isset($this->{$key})) {
                $arr[$key] = $this->{$key};
            }
        }

        return $arr;
    }

    // }}}
}
