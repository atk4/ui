<?php

namespace atk4\ui;

/**
 * Class implements Messages (a visual box).
 *
 * Specify type = info | warning | error | success
 *
 * $page->add([
 *  'Message',
 *  'type'=>'error',
 *  'Unable to save your document',
 *  'text'=>'
 *  ])
 *  ->text->addParagraph('')
 */
class Message extends View
{
    /**
     * Set to info | warning | error | success | positie | negative.
     *
     * @var string
     */
    public $type = null;

    /**
     * Contains a text to be included below.
     */
    public $text = null;

    /**
     * Specify icon to be displayed.
     */
    public $icon = null;

    public $ui = 'message';

    public $defaultTemplate = 'message.html';

    public function init()
    {
        parent::init();

        if ($this->text !== false) {
            if ($this->text) {
                $this->text = $this->add(new Text($this->text));
            } else {
                $this->text = $this->add(new Text());
            }
        }
    }

    public function renderView()
    {
        if ($this->type) {
            $this->addClass($this->type);
        }

        if ($this->icon) {
            $this->add(new Icon($this->icon), 'Icon');
            $this->addClass('icon');
        }

        if ($this->content) {
            $this->template->set('header', $this->content);
            $this->content = null;
        }

        return parent::renderView();
    }
}
