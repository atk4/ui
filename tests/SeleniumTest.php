<?php

use atk4\core\AtkPhpunit;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class SeleniumTest extends AtkPhpunit\TestCase
{
    protected $user_id;
    protected $security_key;

    public function setUp(): void
    {
        if (!$this->user_id) {
            $this->markTestIncomplete('BROWSERSTACK_USER is not set');
        }

        $this->user_id = $_ENV['BROWSERSTACK_USER'];

        $this->security_key = $_ENV['BROWSERSTACK_ACCESSKEY'];
        $this->url = 'https://' . $this->user_id . ':' . $this->security_key . '@hub-cloud.browserstack.com/wd/hub';
        $this->demos = $_ENV['URL'];
    }

    public function testJS()
    {
        $caps = [
            'browser'            => 'IE',
            'browser_version'    => '9.0',
            'os'                 => 'Windows',
            'os_version'         => '7',
            'browserstack.debug' => 'true',
        ];
        $caps['browserstack.local'] = 'true';
        $web_driver = RemoteWebDriver::create(
            $this->url,
            $caps
        );
        $web_driver->get($this->demos . '/button2.php');

        $this->assertFalse($web_driver->executeScript("return $('#b1').is(':visible')"));

        $web_driver->findElement(WebDriverBy::cssSelector('#b2'))->click();
        $this->assertFalse($web_driver->executeScript("return $('#b2').is(':visible')"));

        $web_driver->quit();
    }
}
