<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\AppScopeTrait;
use Atk4\Core\ContainerTrait;
use Atk4\Core\InitializerTrait;
use Atk4\Core\NameTrait;
use Atk4\Core\StaticAddToTrait;
use Atk4\Core\TrackableTrait;

/**
 * Abstract view tree item (used only for View and Callback, you want probably to extend one of these).
 *
 * @property array<string, AbstractView> $elements
 *
 * @method View getOwner()
 */
abstract class AbstractView
{
    use AppScopeTrait;
    use ContainerTrait {
        add as private _add;
    }
    use InitializerTrait {
        init as private _init;
    }
    use NameTrait;
    use StaticAddToTrait;
    use TrackableTrait;

    /**
     * If add() method is called, but current view is not part of render tree yet,
     * then arguments to add() are simply stored in this array. When the view is
     * initialized by calling init() or adding into App or another initialized View,
     * then add() will be re-invoked with the contents of this array.
     *
     * @var array<int, array{self, array}>|null
     */
    protected ?array $_addLater = [];

    /** Will be set to true after rendered. This is so that we don't render view twice. */
    protected bool $_rendered = false;

    /**
     * Called when view becomes part of render tree. You can override it but avoid
     * placing any "heavy processing" here.
     */
    protected function init(): void
    {
        if ($this->name === null) {
            $this->name = 'atk';
        }

        $this->_init();

        if ($this->_addLater !== null) {
            foreach ($this->_addLater as [$object, $args]) {
                $this->add($object, $args);
            }
            $this->_addLater = null;
        }
    }

    /**
     * @return ($object is View ? View : self)
     */
    public function add(self $object, array $args = []): self
    {
        if ('func_num_args'() > 2) { // prevent bad usage
            throw new \Error('Too many method arguments');
        } elseif ($this->_rendered) {
            throw new Exception('You cannot add anything into the view after it was rendered');
        }

        if (!$this->issetApp()) {
            $this->_addLater[] = [$object, $args];

            return $object;
        }

        // will call init() of the object
        $this->_add($object, $args);

        return $object;
    }
}
