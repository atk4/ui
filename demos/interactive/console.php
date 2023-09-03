<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Core\DebugTrait;
use Atk4\Core\Exception as CoreException;
use Atk4\Ui\Button;
use Atk4\Ui\Console;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Message;
use Atk4\Ui\Tabs;
use Atk4\Ui\View;
use Atk4\Ui\VirtualPage;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$testRunClass = AnonymousClassNameCache::get_class(fn () => new class() extends View {
    use DebugTrait;

    /**
     * @return mixed
     */
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

$tabs = Tabs::addTo($app);

$tab = $tabs->addTab('set()');
Header::addTo($tab, [
    'icon' => 'terminal',
    'Console output streaming',
    'subHeader' => 'any output your PHP script produces through console is displayed to user in real-time',
]);
Console::addTo($tab)->set(static function (Console $console) {
    $console->output('Executing test process...');
    sleep(1);
    $console->output('Now trying something dangerous..');
    sleep(1);
    echo 'direct output is captured';

    throw new CoreException('BOOM - exceptions are caught');
});

$tab = $tabs->addTab('runMethod()', static function (VirtualPage $vp) use ($testRunClass) {
    Header::addTo($vp, [
        'icon' => 'terminal',
        'Non-interactive method invocation',
        'subHeader' => 'console can invoke a method, which normally would be non-interactive and can still capture debug output',
    ]);
    Console::addTo($vp)->runMethod($testRunClass::addTo($vp), 'generateReport');
});

$tab = $tabs->addTab('exec() single', static function (VirtualPage $vp) {
    Header::addTo($vp, [
        'icon' => 'terminal',
        'Command execution',
        'subHeader' => 'it is easy to run server-side commands and stream output through console',
    ]);
    $message = Message::addTo($vp, ['This demo may not work', 'type' => 'warning']);
    $message->text->addParagraph('This demo requires Linux OS and will display error otherwise.');
    Console::addTo($vp)->exec('/bin/pwd');
});

$tab = $tabs->addTab('exec() chain', static function (VirtualPage $vp) {
    Header::addTo($vp, [
        'icon' => 'terminal',
        'Command execution',
        'subHeader' => 'it is easy to run server-side commands and stream output through console',
    ]);
    $message = Message::addTo($vp, ['This demo may not work', 'type' => 'warning']);
    $message->text->addParagraph('This demo requires Linux OS and will display error otherwise.');
    Console::addTo($vp)->set(static function (Console $console) {
        $console->exec('/sbin/ping', ['-c', '5', '-i', '1', '192.168.0.1']);
        $console->exec('/sbin/ping', ['-c', '5', '-i', '2', '8.8.8.8']);
        $console->exec('/bin/no-such-command');
    });
});

$tab = $tabs->addTab('composer update', static function (VirtualPage $vp) {
    Header::addTo($vp, [
        'icon' => 'terminal',
        'Command execution',
        'subHeader' => 'it is easy to run server-side commands and stream output through console',
    ]);

    $message = Message::addTo($vp, ['This demo may not work', 'type' => 'warning']);
    $message->text->addParagraph('This demo requires you to have "bash" and "composer" installed and may display error if the process running PHP does not have write access to the "vendor" folder and "composer.*".');

    $button = Button::addTo($message, ['I understand, proceed anyway', 'class.primary big' => true]);

    $console = Console::addTo($vp, ['event' => false]);
    $console->exec('bash', ['-c', 'cd ../..; echo \'Running "composer update" in `pwd`\'; composer --no-ansi update; echo \'Self-updated. OK to refresh now!\'']);

    $button->on('click', $console->jsExecute());
});

$tab = $tabs->addTab('Use after form submit', static function (VirtualPage $vp) {
    Header::addTo($vp, [
        'icon' => 'terminal',
        'How to log form submit process',
        'subHeader' => 'Sometimes you can have long running process after form submit and want to show progress for user...',
    ]);

    session_start();

    $form = Form::addTo($vp);
    $form->addControl('foo');
    $form->addControl('bar');

    $console = Console::addTo($vp, ['event' => false]);
    $console->set(static function (Console $console) {
        $model = $_SESSION['atk4_ui_console_demo'];
        $console->output('Executing process...');
        $console->info(var_export($model->get(), true));
        sleep(1);
        $console->output('Wait...');
        sleep(3);
        $console->output('Process finished');
    });
    $console->js(true)->hide();

    $form->onSubmit(static function (Form $form) use ($console) {
        $_SESSION['atk4_ui_console_demo'] = $form->model; // only option is to store model in session here in demo

        return new JsBlock([
            $console->js()->show(),
            $console->jsExecute(),
        ]);
    });
});
