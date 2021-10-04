<?php

declare(strict_types=1);

namespace Atk4\Ui\Behat;

use Atk4\Core\WarnDynamicPropertyTrait;
use Behat\Behat\Context\Context as BehatContext;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Tester\Result\TestResult;

class DumpContext extends RawMinkContext implements BehatContext
{
    use WarnDynamicPropertyTrait;

    /**
     * Dump current page data when step failed for CI.
     *
     * @AfterStep
     */
    public function dumpPageAfterFailedStep(AfterStepScope $event): void
    {
        $session = $this->getMink()->getSession();

        if ($event->getTestResult()->getResultCode() === TestResult::FAILED) {
            if ($session->getDriver() instanceof \Behat\Mink\Driver\Selenium2Driver) {
                echo 'Dump of failed step:' . "\n";
                echo 'Current page URL: ' . $session->getCurrentUrl() . "\n";
                global $dumpPageCount;
                if (++$dumpPageCount <= 1) { // prevent huge tests output
                    // upload screenshot here if needed in the future
                    // $screenshotData = $session->getScreenshot();
                    // echo 'Screenshot URL: ' . $screenshotUrl . "\n";
                    echo 'Page source: ' . $session->getPage()->getContent() . "\n";
                } else {
                    echo 'Page source: Source code is dumped for the first failed step only.' . "\n";
                }
            }
        }
    }
}
