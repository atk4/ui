<?php

namespace atk4\ui;

/**
 * Class implements Headers.
 *
 * Set size to 1, 2, 3, 4 or 5 if you are looking for Page Header. The size is not affected by
 * header placement on the page. Specify number to constructor like this:
 *
 * $h = new Header(['size'=>1]); // creates <h1>..</h1> header.
 *
 * Alternatively set content headers. Those will emphasize the text in the context of the section.
 *
 * $h = new Header(['size'=>'large']);  // make large header <div class="ui large header">..</div>
 */
class Header extends View
{
    // @inheritdoc
    public $ui = 'header';
    /**
     * Set to 1, 2, .. 5 for page-headers or small/medium/large for content headers.
     *
     * @var int|string
     */
    public $size = null;

    /**
     * Specify icon that will be included in a header.
     *
     * @var string
     */
    public $icon = null;

    /**
     * Include image with a specified source.
     *
     * @var string
     */
    public $image = null;

    /**
     * Will include sub-header.
     *
     * @var string
     */
    public $subHeader = null;

    /**
     * Specify alignment of the header.
     *
     * @var string
     */
    public $aligned = null;

    // @inheritdoc
    public $defaultTemplate = 'header.html';

    /**
     * {@inheritdoc}
     */
    public function renderView()
    {
        if ($this->size) {
            if (is_int($this->size)) {
                $this->element = 'h'.$this->size;
            } else {
                $this->addClass($this->size);
            }
        }

        if ($this->icon) {
            $this->icon = $this->add(new Icon($this->icon), 'Icon');
        }

        if ($this->image) {
            $this->image = $this->add(new Image($this->image), 'Icon');
        }

        if ($this->subHeader) {
            $this->subHeader = $this->add(new View($this->subHeader), 'SubHeader')->addClass('sub header');
        }

        if ($this->aligned) {
            $this->addClass($this->aligned.' aligned');
        }

        if ($this->aligned && ($this->icon || $this->image)) {
            $this->addClass('icon');
        }

        if (!$this->icon && !$this->elements) {
            $this->template->del('has_content');
            $this->template->set('title', $this->content);
        }

        parent::renderView();
    }
}
