<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\GridLayout;
use Atk4\Ui\Message;
use Atk4\Ui\Text;
use Atk4\Ui\View;

class PromotionText extends View
{
    protected function init(): void
    {
        parent::init();

        $t = Text::addTo($this);
        $t->addParagraph(<<<'EOF'
            Agile Toolkit base package includes:
            EOF);

        $t->addHtml(<<<'EOF'
            <ul>
            <li>Over 40 ready-to-use and nicely styled UI components</li>
            <li>Over 10 ways to build interraction</li>
            <li>Over 10 configurable field types, relations, aggregation and much more</li>
            <li>Over 5 SQL and some NoSQL vendors fully supported</li>
            </ul>
            EOF);

        $gl = GridLayout::addTo($this, ['class.stackable divided' => true, 'columns' => 4]);
        Button::addTo($gl, ['Explore UI components', 'class.primary basic fluid' => true, 'iconRight' => 'right arrow'], ['r1c1'])
            ->link('https://github.com/atk4/ui/#bundled-and-planned-components');
        Button::addTo($gl, ['Try out interactive features', 'class.primary basic fluid' => true, 'iconRight' => 'right arrow'], ['r1c2'])
            ->link(['interactive/tabs']);
        Button::addTo($gl, ['Dive into Agile Data', 'class.primary basic fluid' => true, 'iconRight' => 'right arrow'], ['r1c3'])
            ->link('https://git.io/ad');
        Button::addTo($gl, ['More ATK Add-ons', 'class.primary basic fluid' => true, 'iconRight' => 'right arrow'], ['r1c4'])
            ->link('https://github.com/atk4/ui/#add-ons-and-integrations');

        View::addTo($this, ['ui' => 'divider']);

        Message::addTo($this, ['Cool fact!', 'type' => 'info', 'icon' => 'book'])->text
            ->addParagraph('This entire demo is coded in Agile Toolkit and takes up less than 300 lines of very simple code!');
    }
}
