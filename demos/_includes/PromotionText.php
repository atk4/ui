<?php



namespace atk4\ui\demo;

class PromotionText extends \atk4\ui\View
{
    public function init(): void
    {
        parent::init();

        $t = \atk4\ui\Text::addTo($this);
        $t->addParagraph(
            <<< 'EOF'
Agile Toolkit base package includes:
EOF
        );

        $t->addHTML(
            <<< 'HTML'
<ul>
<li>Over 40 ready-to-use and nicely styled UI components</li>
<li>Over 10 ways to build interraction</li>
<li>Over 10 configurable field types, relations, aggregation and much more</li>
<li>Over 5 SQL and some NoSQL vendors fully supported</li>
</ul>

HTML
        );

        $gl = \atk4\ui\GridLayout::addTo($this, [null, 'stackable divided', 'columns' => 4]);
        \atk4\ui\Button::addTo($gl, ['Explore UI components', 'primary basic fluid', 'iconRight' => 'right arrow'], ['r1c1'])
            ->link('https://github.com/atk4/ui/#bundled-and-planned-components');
        \atk4\ui\Button::addTo($gl, ['Try out interactive features', 'primary basic fluid', 'iconRight' => 'right arrow'], ['r1c2'])
            ->link(['interactive/tabs']);
        \atk4\ui\Button::addTo($gl, ['Dive into Agile Data', 'primary basic fluid', 'iconRight' => 'right arrow'], ['r1c3'])
            ->link('https://git.io/ad');
        \atk4\ui\Button::addTo($gl, ['More ATK Add-ons', 'primary basic fluid', 'iconRight' => 'right arrow'], ['r1c4'])
            ->link('https://github.com/atk4/ui/#add-ons-and-integrations');

        \atk4\ui\View::addTo($this, ['ui' => 'divider']);

        \atk4\ui\Message::addTo($this, ['Cool fact!', 'info', 'icon' => 'book'])->text
            ->addParagraph('This entire demo is coded in Agile Toolkit and takes up less than 300 lines of very simple code code!');
    }
}
