<?php



namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

/*
\atk4\ui\Button::addTo($app, ['View Form input split in Accordion section', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['accordion-in-form']);
\atk4\ui\View::addTo($app, ['ui' => 'clearing divider']);
*/

\atk4\ui\Header::addTo($app, ['Nested accordions']);

function addAccordion($view, $max_depth = 2, $level = 0)
{
    $accordion = \atk4\ui\Accordion::addTo($view, ['type' => ['styled', 'fluid']]);

    // static section
    $i1 = $accordion->addSection('Static Text');
    \atk4\ui\Message::addTo($i1, ['This content is added on page loaded', 'ui' => 'tiny message']);
    \atk4\ui\LoremIpsum::addTo($i1, ['size' => 1]);
    if ($level < $max_depth) {
        addAccordion($i1, $max_depth, $level + 1);
    }

    // dynamic section - simple view
    $i2 = $accordion->addSection('Dynamic Text', function ($v) use ($max_depth, $level) {
        \atk4\ui\Message::addTo($v, ['Every time you open this accordion item, you will see a different text', 'ui' => 'tiny message']);
        \atk4\ui\LoremIpsum::addTo($v, ['size' => 2]);
        if ($level < $max_depth) {
            addAccordion($v, $max_depth, $level + 1);
        }
    });

    // dynamic section - form view
    $i3 = $accordion->addSection('Dynamic Form', function ($v) use ($max_depth, $level) {
        \atk4\ui\Message::addTo($v, ['Loading a form dynamically.', 'ui' => 'tiny message']);
        $f = \atk4\ui\Form::addTo($v);
        $f->addField('Email');
        $f->onSubmit(function (\atk4\ui\Form $form) {
            return $form->success('Subscribed ' . $form->model->get('Email') . ' to newsletter.');
        });

        if ($level < $max_depth) {
            addAccordion($v, $max_depth, $level + 1);
        }
    });
}

// add accordion structure
$a = addAccordion($app);
