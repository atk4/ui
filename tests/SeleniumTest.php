<?php
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;


class SeleniumTest extends \PHPUnit_Framework_TestCase {

    protected $user_id;
    protected $security_key;

    public function setUp() {
        $this->user_id = $_ENV['BROWSERSTACK_USER'];
        $this->security_key = $_ENV['BROWSERSTACK_ACCESSKEY'];
        $this->url = "https://" . $this->user_id . ":" . $$htis->security_key . "@hub-cloud.browserstack.com/wd/hub";
    }

    public function testGoogle() {
        $caps = array(
            "browser" => "IE",
            "browser_version" => "9.0",
            "os" => "Windows",
            "os_version" => "7",
            "browserstack.debug" => "true"
        );
        $caps['browserstack.local'] = "true";

        $web_driver = RemoteWebDriver::create(
            $this->url,
            $caps
        );
        $web_driver->get("http://www.google.com");
        print $web_driver->getTitle();
        $element = $web_driver->findElement(WebDriverBy::name("q"));
        if ($element) {
            $element->sendKeys("Browserstack");
            $element->submit();
        }
        $web_driver->quit();
    }

    public function testBrowserStack() {
        $caps = array(
            "browser" => "IE",
            "browser_version" => "9.0",
            "os" => "Windows",
            "os_version" => "7",
            "browserstack.debug" => "true"
        );
        $caps['browserstack.local'] = "true";
        $web_driver = RemoteWebDriver::create(
            $this->url,
            $caps
        );
        $web_driver->get("https://www.browserstack.com");
        print $web_driver->getTitle();
        $web_driver->quit();
    }

    public function testFacebook() {
        $caps = array(
            "browser" => "IE",
            "browser_version" => "9.0",
            "os" => "Windows",
            "os_version" => "7",
            "browserstack.debug" => "true"
        );
        $caps['browserstack.local'] = "true";
        $web_driver = RemoteWebDriver::create(
            $this->url,
            $caps
        );
        $web_driver->get("http://www.facebook.com");
        print $web_driver->getTitle();
        $web_driver->quit();
    }
}
