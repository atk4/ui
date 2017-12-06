<?php


// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Console is a black square component resembling terminal window. It can be programmed
 * to run a job and output results to the user.
 */
class Console extends View implements \Psr\Log\LoggerInterface
{
    public $ui = 'inverted black segment';
    public $element = 'pre';

    /**
     * Will be set to $true while executing callback. Some methods
     * will use this to automatically schedule their own callback
     * and allowing you a cleaner syntax, such as
     *
     * $console->setModel($user, 'generateReport');
     *
     * @var boolean
     */
    protected $sseInProgress = false;

    /**
     * Stores object jsSSE which is used for communication
     *
     * @var jsSSE
     */
    public $sse;

    /**
     * Supply callback which will be executed in "background" sending
     * your console output.
     *
     * @param $callback string
     *
     * @return $this
     */
    public $_output_bypass = false;

    public function set($callback)
    {
        $this->sse = $this->add('jsSSE');
        //$this->output('bleh');
        $this->sse->set(function () use ($callback) {
            $this->sseInProgress = true;

            try {
                $tmp = true;


                ob_implicit_flush(true);
                ob_start(function($content) {
                    if ($this->_output_bypass) {
                        return $content;
                    }

                    $output = '';
                    $this->sse->echoFunction = function($str) use (&$output) {
                        $output .= $str;
                    };
                    $this->output($content);
                    $this->sse->echoFunction = false;

                    return $output;
                }, 2);



                call_user_func($callback, $this);
            } catch (\atk4\core\Exception $e) {
                $lines = explode("\n", $e->getHTMLText());

                foreach ($lines as $line) {
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
     * Output a single line to the console.
     *
     * @param $text string
     *
     * @return $this
     */
    public function output($text)
    {
        $this->_output_bypass=true;
        $this->sse->send($this->js()->append(htmlspecialchars($text).'<br/>'));
        $this->_output_bypass=false;
        return $this;
    }

    /**
     * Output un-escaped HTML line. Use this to send HTML.
     *
     * @param $text string
     *
     * @return $this
     */
    public function outputHTML($text)
    {
        $this->_output_bypass=true;
        $this->sse->send($this->js()->append($text.'<br/>'));
        $this->_output_bypass=false;
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
            $this->set(function () {
                $this->runCommand($exec, $args);
            });

            return;
        }


        // not implemented here
        //
        //
    }
     */

    /**
     * Execute method of a certain model. That's a short-hand method
     * for running:.
     *
     * $app->add('Console')->setModel(new User($db), 'generateReports');
     *
     * You can enable output from inside your method if you:
     *
     *  - implement \atk4\core\DebugTrait in your model
     *  - use $this->debug() or $this->info()
     *  - if you wish to get log from other objects, be sure to switch debug on with $obj->debug = true;
     *
     * @param $model \atk4\data\Model
     * @param $method string 
     * @param $args array
     */
    public function setModel(\atk4\data\Model $model, string $method, $args = [])
    {
        if (!$this->sseInProgress) {
            $this->set(function () use ($model, $method, $args) {
                $this->setModel($model, $method, $args);
            });

            return;
        }

        // temporarily override app logging
        if (isset($model->app)) {
            $old_logger = $model->app->logger;
            $model->app->logger = $this;
        }

        $this->output('--[ Executing '.get_class($model).'->'.$method.' ]--------------');
        $model->debug = true;
        $result = call_user_func_array([$model, $method], $args);
        $this->output('--[ Result: '.json_encode($result).' ]------------');

        if (isset($model->app)) {
            $model->app->logger = $old_logger;
        }
    }

    public function emergency($str, $args = [])
    {
        return $this->outputHTML("<font color='pink'>".htmlspecialchars($str).'</font>');
    }

    public function alert($str, $args = [])
    {
        return $this->outputHTML("<font color='pink'>".htmlspecialchars($str).'</font>');
    }

    public function critical($str, $args = [])
    {
        return $this->outputHTML("<font color='pink'>".htmlspecialchars($str).'</font>');
    }

    public function error($str, $args = [])
    {
        return $this->outputHTML("<font color='pink'>".htmlspecialchars($str).'</font>');
    }

    public function warning($str, $args = [])
    {
        return $this->outputHTML("<font color='pink'>".htmlspecialchars($str).'</font>');
    }

    public function notice($str, $args = [])
    {
        return $this->outputHTML("<font color='yellow'>".htmlspecialchars($str).'</font>');
    }

    public function info($str, $args = [])
    {
        return $this->outputHTML("<font color='gray'>".htmlspecialchars($str).'</font>');
    }

    public function debug($str, $args = [])
    {
        return $this->outputHTML("<font color='cyan'>".htmlspecialchars($str).'</font>');
    }

    public function log($level, $str, $args = [])
    {
        return $this->$level($str, $args);
    }
}
