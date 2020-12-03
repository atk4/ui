<?php

declare(strict_types=1);

namespace Atk4\Ui\Behat;

use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Testwork\Tester\Result\TestResult;

/**
 * Dump page data when failed.
 */
class ContextDump extends Context
{
    /**
     * Dump current page data when step failed for CI.
     *
     * @AfterStep
     */
    public function dumpPageAfterFailedStep(AfterStepScope $event): void
    {
        if ($event->getTestResult()->getResultCode() === TestResult::FAILED) {
            if ($this->getSession()->getDriver() instanceof \Behat\Mink\Driver\Selenium2Driver) {
                echo 'Dump of failed step:' . "\n";
                echo 'Current page URL: ' . $this->getSession()->getCurrentUrl() . "\n";
                global $dumpPageCount;
                if (++$dumpPageCount <= 1) { // prevent huge tests output
                    // upload screenshot here if needed in the future
                    // $screenshotData = $this->getSession()->getScreenshot();
                    // echo 'Screenshot URL: ' . $screenshotUrl . "\n";
                    echo 'Page source: ' . $this->getSession()->getPage()->getContent() . "\n";
                } else {
                    echo 'Page source: Source code is dumped for the first failed step only.' . "\n";
                }
            }
        }
    }
}
