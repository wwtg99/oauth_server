<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/4
 * Time: 16:11
 */

namespace Flight2wwu\Schedule;


class ScheduleManager {

    const LOCK_FILE = 'schedule.lock';

    /**
     * @var array
     */
    private $app_config = [];

    /**
     * @var array
     */
    private $schedules = [];

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var string
     */
    private $support = '';

    /**
     * @var string
     */
    private $time = '';

    /**
     * @var string
     */
    private $engine = '';

    /**
     * @param string $conf
     */
    function __construct($conf)
    {
        $this->app_config = require "$conf";
        $sche = $this->app_config['schedule'];
        $this->support = $sche['support'];
        $this->time = $sche['time'];
        $this->engine = $sche['engine'];
        $f = $sche['config'];
        if (file_exists($f)) {
            $fj = file_get_contents($f);
            $this->config = json_decode($fj, true);
        }
    }

    /**
     * Register all schedules
     */
    public function register()
    {
        foreach ($this->config as $s) {
            if (!$s['enabled']) {
                continue;
            }
            $id = $s['id'];
            $name = $s['name'];
            $cls = $s['class_name'];
            $inv = $s['interval'];
            try {
                $rc = new \ReflectionClass($cls);
                $ins = $rc->newInstance();
                if ($ins instanceof ISchedule) {
                    $ins->register($this->app_config);
                    $this->schedules[$id] = ['name' => $name, 'instance' => $ins, 'interval' => $inv];
                }
            } catch (\Exception $e) {
                getLog()->warning("Schedule $name does not exists");
            }
        }
    }

    /**
     * Run all schedules
     */
    public function run()
    {
        $lock = $this->loadLock();
        foreach ($this->schedules as $id => $sche) {
            $runf = false;
            $now = new \DateTime();
            if (array_key_exists($id, $lock)) {
                $last_time = new \DateTime($lock[$id]);
                $inv = $sche['interval'];
                $di = $now->diff($last_time);
                if ($di->d >= $inv) {
                    $runf = true;
                }
            } else {
                $runf = true;
            }
            if ($runf) {
                $ins = $sche['instance'];
                if ($ins instanceof ISchedule) {
                    $ins->run();
                    $lock[$id] = $now->format('Y-m-d H:i:s');
                }
            }
        }
        $this->writeLock($lock);
    }

    /**
     * Enable schedule service
     */
    public function enableService()
    {
        if ($this->support == 'crontab') {
            $cont = $this->time . ' php ' . realpath(ROOT) . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . $this->engine . "\n";
            $f = TMP . 'crontab';
            file_put_contents($f, $cont);
            exec("crontab $f");
        }
    }

    /**
     * Disable all schedules
     */
    public function disableService()
    {
        if ($this->support == 'crontab') {
            exec("crontab -r");
        }
    }

    /**
     * Read lock file
     *
     * @return array
     */
    private function loadLock()
    {
        $f = TMP . self::LOCK_FILE;
        if (file_exists($f)) {
            $fj = file_get_contents($f);
            return json_decode($fj, true);
        }
        return [];
    }

    /**
     * Write lock file
     *
     * @param array $arr
     */
    private function writeLock(array $arr)
    {
        if (!file_exists(TMP)) {
            mkdir(TMP, 0777, true);
        }
        $f = TMP . self::LOCK_FILE;
        file_put_contents($f, json_encode($arr, JSON_PRETTY_PRINT));
    }
} 