<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/19
 * Time: 10:54
 */

namespace Flight2wwu\Component\Database;


use Flight2wwu\Common\ServiceProvider;

class MedooDB implements ServiceProvider
{

    /**
     * @var array
     */
    private $connections = [];

    /**
     * @var string
     */
    private $current = '';

    /**
     * Called after register.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Called after all class is registered.
     *
     * @return void
     */
    public function boot()
    {
        $this->reconnect();
    }

    /**
     * Execute query return all rows.
     *
     * @param string $query
     * @param array $data
     * @return array|bool
     */
    public function query($query, array $data = array())
    {
        $stmt = $this->prepare($query);
        $stmt = $this->executePrepare($stmt, $data);
        if ($stmt) {
            return $stmt->fetchAll();
        }
        return false;
    }

    /**
     * Execute query return first row.
     *
     * @param string $query
     * @param array $data
     * @return bool|array
     */
    public function queryOne($query, array $data = array())
    {
        $stmt = $this->prepare($query);
        $stmt = $this->executePrepare($stmt, $data);
        if ($stmt) {
            return $stmt->fetch();
        }
        return false;
    }

    /**
     * Execute update, insert or delete.
     *
     * @param string $query
     * @param array $data
     * @return bool|int
     */
    public function exec($query, array $data = array())
    {
        $stmt = $this->prepare($query);
        $stmt = $this->executePrepare($stmt, $data);
        if ($stmt) {
            return $stmt->rowCount();
        }
        return false;
    }

    /**
     * Prepare query
     *
     * @param string $query
     * @return \PDOStatement
     */
    public function prepare($query)
    {
        $query = str_replace(';', ' ', $query);
        $statement = $this->getConnection()->pdo->prepare($query);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        return $statement;
    }

    /**
     * Execute prepared query.
     *
     * @param \PDOStatement $statement
     * @param array $data
     * @return bool|\PDOStatement
     */
    public function executePrepare(\PDOStatement $statement, array $data = array())
    {
        $this->bindValue($statement, $data);
        try {
            $re = $statement->execute();
            if (!$re) {
                $this->logError($this->getLastError(), $this->getConnection()->last_query());
                return false;
            }
            return $statement;
        } catch (\Exception $e) {
            $this->logError($this->getLastError(), $this->getConnection()->last_query());
            return false;
        }
    }

    /**
     * @param array $conf
     * @param string $name
     * @return $this
     */
    public function connect(array $conf, $name = 'main')
    {
        $db_conf = [
            'database_type' => $conf['driver'],
            'database_name' => $conf['dbname'],
            'server' => $conf['host'],
            'username' => $conf['user'],
            'password' => $conf['password'],
            'port' => $conf['port'],
            'charset' => 'utf8',
        ];
        if (array_key_exists('option', $conf)) {
            $db_conf['option'] = $conf['option'];
        }
        if (array_key_exists('prefix', $conf)) {
            $db_conf['prefix'] = $conf['prefix'];
        }
        $database = new \medoo($db_conf);
        $this->connections[$name] = $database;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     * @throws \Exception
     */
    public function reconnect($name = 'main')
    {
        if (!array_key_exists($name, $this->connections)) {
            $conf = \Flight::get('database');
            if (!array_key_exists($name, $conf)) {
                throw new \Exception("database config $name is not exists");
            }
            $main = $conf[$name];
            $this->connect($main, $name);
        }
        $this->current = $name;
        return $this;
    }

    /**
     * @param string $name
     * @return \medoo
     * @throws \Exception
     */
    public function getConnection($name = null)
    {
        if (is_null($name)) {
            $name = $this->current;
        }
        if (count($this->connections) <= 0) {
            throw new \Exception('no connections yet');
        }
        if (!array_key_exists($name, $this->connections)) {
            $this->current = key($this->connections);
        }
        return $this->connections[$this->current];
    }

    /**
     * @return array
     */
    public function getLastError()
    {
        return $this->getConnection()->error();
    }

    /**
     * @param \PDOStatement $statement
     * @param array $data  [key1=>[value1, type1], key1=>value2, ...]
     */
    private function bindValue(\PDOStatement $statement, array $data)
    {
        if ($statement) {
            foreach ($data as $k => $v) {
                if (is_array($v) && count($v) > 1) {
                    $statement->bindValue($k, $v[0], $v[1]);
                } else {
                    $statement->bindValue($k, $v);
                }
            }
        }
    }

    /**
     * @param array $error
     * @param string $sql
     */
    private function logError(array $error, $sql = '')
    {
        $logger = getLog();
        $logger->changeLogger('database')->error("Code (" . $error[1] . ') ' . $error[2] . " by $sql");
        $logger->changeLogger('main');
    }
} 