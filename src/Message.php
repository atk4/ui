<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * Class implements Messages (a visual box).
 *
 * Specify type = info | warning | error | success
 *
 * Message::addTo($page, [
 *  'type' => 'error',
 *  'text' => 'Unable to save your document',
 *  ])
 *  ->text->addParagraph('')
 */
class Message extends View
{
    /** @var string Set to info | warning | error | success | positie | negative. */
    public $type;

    /** @var Text|false Contains a text to be included below. */
    public $text;

    /** @var string Specify icon to be displayed. */
    public $icon;

    public $ui = 'message';

    public $defaultTemplate = 'message.html';

    protected function init(): void
    {
        parent::init();

        if ($this->text !== false) {
            if ($this->text) {
                $this->text = Text::addTo($this, [$this->text]);
            } else {
                $this->text = Text::addTo($this);
            }
        }
    }

    protected function renderView(): void
    {
        if ($this->type) {
            $this->addClass($this->type);
        }

        if ($this->icon) {
            if (!is_object($this->icon)) {
                $this->icon = new Icon($this->icon);
            }
            $this->add($this->icon, 'Icon');
            $this->addClass('icon');
        }

        if ($this->content) {
            $this->template->set('header', $this->content);
            $this->content = null;
        }

        parent::renderView();
    }
}
