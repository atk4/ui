<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

/** @var \Atk4\Ui\View $testRunClass */
$testRunClass = get_class(new class() extends \Atk4\Ui\View {
    use \Atk4\Core\DebugTrait;

    public function generateReport()
    {
        $this->log('info', 'Console will automatically pick up output from all DebugTrait objects');
        $this->debug('debug {foo}', ['foo' => 'bar']);
        $this->emergency('emergency {foo}', ['foo' => 'bar']);
        $this->alert('alert {foo}', ['foo' => 'bar']);
        $this->critical('critical {foo}', ['foo' => 'bar']);
        $this->error('error {foo}', ['foo' => 'bar']);
        $this->warning('warning {foo}', ['foo' => 'bar']);
        $this->notice('notice {foo}', ['foo' => 'bar']);
        $this->info('info {foo}', ['foo' => 'bar']);

        return 123;
    }
});

$tabs = \Atk4\Ui\Tabs::addTo($app);

$tab = $tabs->addTab('set()');
\Atk4\Ui\Header::addTo($tab, [
    'icon' => 'terminal',
    'Console output streaming',
    'subHeader' => 'any output your PHP script produces through console is displayed to user in real-time',
]);
\Atk4\Ui\Console::addTo($tab)->set(function ($console) {
    $console->output('Executing test process...');
    sleep(1);
    $console->output('Now trying something dangerous..');
    sleep(1);
    echo 'direct output is captured';

    throw new \Atk4\Core\Exception('BOOM - exceptions are caught');
});

$tab = $tabs->addTab('runMethod()', function ($tab) use ($testRunClass) {
    \Atk4\Ui\Header::addTo($tab, [
        'icon' => 'terminal',
        'Non-interractive method invocation',
        'subHeader' => 'console can invoke a method, which normaly would be non-interractive and can still capture debug output',
    ]);
    \Atk4\Ui\Console::addTo($tab)->runMethod($testRunClass::addTo($tab), 'generateReport');
});

$tab = $tabs->addTab('exec() single', function ($tab) {
    \Atk4\Ui\Header::addTo($tab, [
        'icon' => 'terminal',
        'Command execution',
        'subHeader' => 'it is easy to run server-side commands and stream output through console',
    ]);
    $message = \Atk4\Ui\Message::addTo($tab, ['This demo may not work', 'warning']);
    $message->text->addParagraph('This demo requires Linux OS and will display error otherwise.');
    \Atk4\Ui\Console::addTo($tab)->exec('/bin/pwd');
});

$tab = $tabs->addTab('exec() chain', function ($tab) {
    \Atk4\Ui\Header::addTo($tab, [
        'icon' => 'terminal',
        'Command execution',
        'subHeader' => 'it is easy to run server-side commands and stream output through console',
    ]);
    $message = \Atk4\Ui\Message::addTo($tab, ['This demo may not work', 'warning']);
    $message->text->addParagraph('This demo requires Linux OS and will display error otherwise.');
    \Atk4\Ui\Console::addTo($tab)->set(function ($console) {
        $console->exec('/sbin/ping', ['-c', '5', '-i', '1', '192.168.0.1']);
        $console->exec('/sbin/ping', ['-c', '5', '-i', '2', '8.8.8.8']);
        $console->exec('/bin/no-such-command');
    });
});

$tab = $tabs->addTab('composer update', function ($tab) {
    \Atk4\Ui\Header::addTo($tab, [
        'icon' => 'terminal',
        'Command execution',
        'subHeader' => 'it is easy to run server-side commands and stream output through console',
    ]);

    $message = \Atk4\Ui\Message::addTo($tab, ['This demo may not work', 'warning']);
    $message->text->addParagraph('This demo requires you to have "bash" and "composer" installed and may display error if the process running PHP does not have write access to the "vendor" folder and "composer.*".');

    $button = \Atk4\Ui\Button::addTo($message, ['I understand, proceed anyway', 'primary big']);

    $console = \Atk4\Ui\Console::addTo($tab, ['event' => false]);
    $console->exec('bash', ['-c', 'cd ../..; echo "Running \'composer update\' in `pwd`"; composer --no-ansi update; echo "Self-updated. OK to refresh now!"']);

    $button->on('click', $console->jsExecute());
});

$tab = $tabs->addTab('Use after form submit', function ($tab) {
    \Atk4\Ui\Header::addTo($tab, [
        'icon' => 'terminal',
        'How to log form submit process',
        'subHeader' => 'Sometimes you can have long running process after form submit and want to show progress for user...',
    ]);

    session_start();

    $form = \Atk4\Ui\Form::addTo($tab);
    $form->addControls(['foo', 'bar']);

    $console = \Atk4\Ui\Console::addTo($tab, ['event' => false]);
    $console->set(function ($console) {
        $model = $_SESSION['data'];
        $console->output('Executing process...');
        $console->info(var_export($model->get(), true));
        sleep(1);
        $console->output('Wait...');
        sleep(3);
        $console->output('Process finished');
    });
    $console->js(true)->hide();

    $form->onSubmit(function (\Atk4\Ui\Form $form) use ($console) {
        $_SESSION['data'] = $form->model; // only option is to store model in session here in demo

        return [
            $console->js()->show(),
            $console->jsExecute(),
        ];
    });
});
