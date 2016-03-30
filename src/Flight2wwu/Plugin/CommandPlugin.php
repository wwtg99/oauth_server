<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/19
 * Time: 13:12
 */

namespace Flight2wwu\Plugin;


use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

abstract class CommandPlugin implements IPlugin
{

    /**
     * @var bool
     */
    protected $async = true;

    /**
     * @var string
     */
    protected $cmd = '';

    /**
     * @var string
     */
    protected $out = '';

    /**
     * Execute command
     *
     * @return int|mixed
     * @throws \Exception
     */
    public function exec()
    {
        $args = func_get_args();
        if (!$this->cmd) {
            throw new \Exception('no cmd provided');
        }
        $build = new ProcessBuilder();
        $pro = $build->setPrefix($this->cmd)->setArguments($args)->getProcess();
        $rm = new \ReflectionMethod($this, 'output');
        $func = $rm->getClosure($this);
        $re = 0;
        if ($this->async) {
            $pro->start($func);
        } else {
            try {
                $pro->mustRun($func);
                $this->out = $pro->getOutput();
                $re = $this->out;
            } catch (ProcessFailedException $e) {
                getLog()->error($e->getMessage());
                $re = 1;
            }
        }
        return $re;
    }

    /**
     * Output function
     *
     * @param $type
     * @param $data
     */
    protected function output($type, $data)
    {
        if ($type === Process::ERR) {
            getLog()->error($data);
        } else {
            getLog()->info($data);
        }
    }

} 