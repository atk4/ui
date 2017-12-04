<?php


// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Console is a black square component resembling terminal window. It can be programmed
 * to run a job and output results to the user.
 */
class Console extends View
    implements \Psr\Log\LoggerInterface
{
    public $ui = 'inverted black segment';
    public $element = 'pre';

    protected $sseInProgress = false;

    public $sse;

    public function set($callback) {
        $this->sse = $this->add('jsSSE');
        //$this->output('bleh');
        $this->sse->set(function() use($callback) {
            $this->sseInProgress = true;
            try {
                call_user_func($callback, $this);
            } catch (\atk4\core\Exception $e) {
                $lines = explode("\n", $e->getHTMLText());

                foreach($lines as $line) {
                    $this->outputHTML($line);
                }

            } catch (\Error $e) {
                $this->output('Error: '.$e->getMessage());
            } catch (\Exception $e) {
                $this->output('Exception: '.$e->getMessage());
            }
            $this->sseInProgress = false;
        });
        $this->js(true, $this->sse);
        return $this;
    }

    /**
     * Output a single line to the console
     */
    public function output($text) {
        $this->sse->send($this->js()->append(htmlspecialchars($text).'<br/>'));
    }

    /**
     * Output un-escaped HTML line
     */
    public function outputHTML($text) {
        $this->sse->send($this->js()->append($text.'<br/>'));
    }

    /**
     * Executes command passing along escaped arguments.
     *
     * Will also stream stdout / stderr as the comand executes.
     * once command terminates method will return the exit code.
     *
     * This method can be executed from inside callback or
     * without it.
     */

    /*
    public function runCommand($exec, $args = [])
    {
        if (!$this->sseInProgress) {
            $this->set(function() {
                $this->runCommand($exec, $args);
            });
            return null;
        }


        // not implemented here
        //
        //
    }
     */

    /**
     * Execute method of a certain model. That's a short-hand method
     * for running:
     *
     * $app->add('Console')->setModel(new User($db), 'generateReports');
     *
     */
    public function setModel(\atk4\data\Model $model, string $method, $args = []) {
        if (!$this->sseInProgress) {
            $this->set(function() use ($model, $method, $args) {
                $this->setModel($model, $method, $args);
            });
            return null;
        }

        // temporarily override app logging
        $old_logger = $model->app->logger;
        $model->app->logger = $this;

        $this->output('--[ Executing '.get_class($model).'->'.$method.' ]--------------');
        $model->debug = true;
        $result = call_user_func_array([$model, $method], $args);
        $this->output('--[ Result: '.json_encode($result).' ]------------');

        $model->app->logger = $old_logger;
    }

    function emergency($str, $args = []) {
        return $this->outputHTML("<font color='pink'>".htmlspecialchars($str)."</font>");
    }

    function alert($str, $args = []) {
        return $this->outputHTML("<font color='pink'>".htmlspecialchars($str)."</font>");
    }

    function critical($str, $args = []) {
        return $this->outputHTML("<font color='pink'>".htmlspecialchars($str)."</font>");
    }

    function error($str, $args = []) {
        return $this->outputHTML("<font color='pink'>".htmlspecialchars($str)."</font>");
    }

    function warning($str, $args = []) {
        return $this->outputHTML("<font color='pink'>".htmlspecialchars($str)."</font>");
    }

    function notice($str, $args = []) {
        return $this->outputHTML("<font color='yellow'>".htmlspecialchars($str)."</font>");
    }

    function info($str, $args = []) {
        return $this->outputHTML("<font color='gray'>".htmlspecialchars($str)."</font>");
    }

    function debug($str, $args = []) {
        return $this->outputHTML("<font color='cyan'>".htmlspecialchars($str)."</font>");
    }

    function log($level, $str, $args = []) {
        return $this->$level($str, $args);
    }

    // Methods to be called only from inside callback
}
