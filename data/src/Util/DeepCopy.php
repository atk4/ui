<?php

declare(strict_types=1);

namespace Atk4\Data\Util;

use Atk4\Core\DebugTrait;
use Atk4\Data\Model;
use Atk4\Data\Reference\HasMany;
use Atk4\Data\Reference\HasOne;

/**
 * Class DeepCopy implements copying records between two models:.
 *
 * $dc = new DeepCopy();
 *
 * $dc->from($user);
 * $dc->to(new ArchivedUser());
 * $dc->with('AuditLog');
 * $dc->copy();
 */
class DeepCopy
{
    use DebugTrait;

    /** @const string */
    public const HOOK_AFTER_COPY = self::class . '@afterCopy';

    /**
     * @var Model from which we want to copy records
     */
    protected $source;

    /**
     * @var Model in which we want to copy records into
     */
    protected $destination;

    /**
     * @var array containing references which we need to copy. May contain sub-arrays: ['Invoices' => ['Lines']]
     */
    protected $references = [];

    /**
     * @var array contains array similar to references but containing list of excluded fields:
     *            e.g. ['Invoices' => ['Lines' => ['vat_rate_id']]]
     */
    protected $exclusions = [];

    /**
     * @var array contains array similar to references but containing list of callback methods to transform fields/values:
     *            e.g. ['Invoices' => ['Lines' => function($data){
     *            $data['exchanged_amount'] = $data['amount'] * getExRate($data['date'], $data['currency']);
     *            return $data;
     *            }]]
     */
    protected $transforms = [];

    /**
     * @var array while copying, will record mapped records in format [$table => ['old_id' => 'new_id']]
     */
    public $mapping = [];

    /**
     * Set model from which to copy records.
     *
     * @return $this
     */
    public function from(Model $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Set model in which to copy records into.
     *
     * @return $this
     */
    public function to(Model $destination)
    {
        $this->destination = $destination;

        if (!$this->destination->persistence) {
            $this->source->persistence->add($this->destination);
        }

        return $this;
    }

    /**
     * Set references to copy.
     *
     * @return $this
     */
    public function with(array $references)
    {
        $this->references = $references;

        return $this;
    }

    /**
     * Specifies which fields shouldn't be copied. May also contain arrays
     * for related entries.
     * ->excluding(['name', 'address_id' => ['city']]);.
     *
     * @return $this
     */
    public function excluding(array $exclusions)
    {
        $this->exclusions = $exclusions;

        return $this;
    }

    /**
     * Specifies which models data should be transformed while copying.
     * May also contain arrays for related entries.
     *
     * ->transformData(
     *      [function($data){ // for Client entity
     *          $data['name'] => $data['last_name'].' '.$data['first_name'];
     *          unset($data['first_name'], $data['last_name']);
     *          return $data;
     *      }],
     *      'Invoices' => ['Lines' => function($data){ // for nested Client->Invoices->Lines hasMany entity
     *              $data['exchanged_amount'] = $data['amount'] * getExRate($data['date'], $data['currency']);
     *              return $data;
     *          }]
     *  );
     *
     * @return $this
     */
    public function transformData(array $transforms)
    {
        $this->transforms = $transforms;

        return $this;
    }

    /**
     * Will extract non-numeric keys from the array.
     */
    protected function extractKeys(array $array): array
    {
        $result = [];
        foreach ($array as $key => $val) {
            if (is_int($key)) {
                $result[$val] = [];
            } else {
                $result[$key] = $val;
            }
        }

        return $result;
    }

    /**
     * Copy records.
     */
    public function copy(): Model
    {
        return $this->destination->atomic(function () {
            return $this->_copy(
                $this->source,
                $this->destination,
                $this->references,
                $this->exclusions,
                $this->transforms
            )->reload();
        });
    }

    /**
     * Internal method for copying records.
     *
     * @param array $exclusions fields to exclude
     * @param array $transforms callbacks for data transforming
     *
     * @return Model Destination model
     */
    protected function _copy(Model $source, Model $destination, array $references, array $exclusions, array $transforms): Model
    {
        try {
            // Perhaps source was already copied, then simply load destination model and return
            if (isset($this->mapping[$source->table]) && isset($this->mapping[$source->table][$source->getId()])) {
                $this->debug('Skipping ' . get_class($source));

                $destination = $destination->load($this->mapping[$source->table][$source->getId()]);
            } else {
                $this->debug('Copying ' . get_class($source));

                $data = $source->get();

                // exclude not needed field values
                // see self::excluding()
                foreach ($this->extractKeys($exclusions) as $key => $val) {
                    unset($data[$key]);
                }

                // do data transformation from source to destination
                // see self::transformData()
                if (isset($transforms[0]) && $transforms[0] instanceof \Closure) {
                    $data = $transforms[0]($data);
                }

                // TODO add a way here to look for duplicates based on unique fields
                // foreach($destination->unique fields) { try load by

                // if we still have id field, then remove it
                unset($data[$source->id_field]);

                // Copy fields as they are
                $destination = $destination->createEntity();
                foreach ($data as $key => $val) {
                    if ($destination->hasField($key) && $destination->getField($key)->isEditable()) {
                        $destination->set($key, $val);
                    }
                }
            }
            $destination->hook(self::HOOK_AFTER_COPY, [$source]);

            // Look for hasOne references that needs to be mapped. Make sure records can be mapped, or copy them
            foreach ($this->extractKeys($references) as $ref_key => $ref_val) {
                $this->debug("Considering {$ref_key}");

                if ($source->hasRef($ref_key) && ($ref = $source->getRef($ref_key)) instanceof HasOne) {
                    $this->debug("Proceeding with {$ref_key}");

                    // load destination model through $source
                    $source_table = $ref->refModel()->table;

                    if (
                        isset($this->mapping[$source_table])
                        && array_key_exists($source->get($ref_key), $this->mapping[$source_table])
                    ) {
                        // no need to deep copy, simply alter ID
                        $destination->set($ref_key, $this->mapping[$source_table][$source->get($ref_key)]);
                        $this->debug(' already copied ' . $source->get($ref_key) . ' as ' . $destination->get($ref_key));
                    } else {
                        // hasOne points to null!
                        $this->debug('Value is ' . $source->get($ref_key));
                        if (!$source->get($ref_key)) {
                            $destination->set($ref_key, $source->get($ref_key));

                            continue;
                        }

                        // pointing to non-existent record. Would need to copy
                        try {
                            $destination->set(
                                $ref_key,
                                $this->_copy(
                                    $source->ref($ref_key),
                                    $destination->refModel($ref_key),
                                    $ref_val,
                                    $exclusions[$ref_key] ?? [],
                                    $transforms[$ref_key] ?? []
                                )->getId()
                            );
                            $this->debug(' ... mapped into ' . $destination->get($ref_key));
                        } catch (DeepCopyException $e) {
                            $this->debug('escalating a problem from ' . $ref_key);

                            throw $e->addDepth($ref_key);
                        }
                    }
                }
            }

            // Next copy our own data
            $destination->save();

            // Store mapping
            $this->mapping[$source->table][$source->getId()] = $destination->getId();
            $this->debug(' .. copied ' . get_class($source) . ' ' . $source->getId() . ' ' . $destination->getId());

            // Next look for hasMany relationships and copy those too

            foreach ($this->extractKeys($references) as $ref_key => $ref_val) {
                if ($source->hasRef($ref_key) && ($ref = $source->getRef($ref_key)) instanceof HasMany) {
                    // No mapping, will always copy
                    foreach ($source->ref($ref_key) as $ref_model) {
                        $this->_copy(
                            $ref_model,
                            $destination->ref($ref_key),
                            $ref_val,
                            $exclusions[$ref_key] ?? [],
                            $transforms[$ref_key] ?? []
                        );
                    }
                }
            }

            return $destination;
        } catch (DeepCopyException $e) {
            throw $e;
        } catch (\Atk4\Core\Exception $e) {
            $this->debug('noticed a problem');

            throw (new DeepCopyException('Problem cloning model', 0, $e))
                ->addMoreInfo('source', $source)
                ->addMoreInfo('source_info', $source->__debugInfo())
                ->addMoreInfo('source_data', $source->get())
                ->addMoreInfo('destination', $destination)
                ->addMoreInfo('destination_info', $destination->__debugInfo())
                ->addMoreInfo('depth', $e->getParams()['field'] ?? '?');
        }
    }
}
