<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * TODO move to trait and this class should be migrated to Text.
 */
class ViewWithContent extends View
{
    /** @var string|null Set static contents of this view. */
    public $content;

    /**
     * @param array<0|string, mixed>|string $label
     */
    public function __construct($label = [])
    {
        if (func_num_args() > 1) { // prevent bad usage
            throw new \Error('Too many method arguments');
        }

        $defaults = is_array($label) ? $label : [$label];

        if (array_key_exists(0, $defaults)) {
            $defaults['content'] = $defaults[0];
            unset($defaults[0]);
        }

        parent::__construct($defaults);
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function set($content)
    {
        if (func_num_args() > 1) { // prevent bad usage
            throw new Exception('Only one argument is needed by ViewWithContent::set()');
        }

        if (!is_string($content) && $content !== null) { // @phpstan-ignore-line
            throw (new Exception('Not sure what to do with argument'))
                ->addMoreInfo('this', $this)
                ->addMoreInfo('arg', $content);
        }

        $this->content = $content;

        return $this;
    }

    protected function recursiveRender(): void
    {
        parent::recursiveRender();

        if ($this->content !== null) {
            $this->template->append('Content', $this->content);
        }
    }
}
