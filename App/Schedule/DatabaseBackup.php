<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/4
 * Time: 17:05
 */

namespace App\Schedule;

use Flight2wwu\Schedule\ISchedule;

class DatabaseBackup implements ISchedule
{
    /**
     * @var array
     */
    private $conf = [];

    /**
     * @var string
     */
    private $backup_dir = '';

    /**
     * Call this function each time
     *
     * @return mixed
     */
    public function run()
    {
        switch ($this->conf['driver']) {
            case 'pgsql': $this->backupPgsql(); break;
        }
    }

    /**
     * Register schedule
     *
     * @param array $conf
     * @return mixed
     */
    public function register($conf)
    {
        $db = $conf['database'];
        if (array_key_exists('backup', $db)) {
            $this->conf = $db['backup'];
        } else {
            $this->conf = $db['main'];
        }
        $this->backup_dir = STORAGE . 'backup';
        if (!file_exists($this->backup_dir)) {
            mkdir($this->backup_dir, 0777, true);
        }
    }

    private function backupPgsql()
    {
        $host = array_key_exists('host', $this->conf) ? ('-h ' . $this->conf['host']) : '';
        $dbname = array_key_exists('dbname', $this->conf) ? ('-d ' . $this->conf['dbname']) : '';
        $user = array_key_exists('user', $this->conf) ? ('-U ' . $this->conf['user']) : '';
        $port = array_key_exists('port', $this->conf) ? ('-p ' . $this->conf['port']) : '';
        $outd = $this->backup_dir . DIRECTORY_SEPARATOR . date('Y-m-d_H-i-s');
        $cmd = "pg_dump $host $port $dbname $user -b -w -Fd -f $outd";
        $re = exec($cmd);
    }

}