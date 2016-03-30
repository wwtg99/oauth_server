<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/9
 * Time: 15:19
 */

namespace Flight2wwu\Component\Database;

use Flight2wwu\Common\ServiceProvider;
use Flight2wwu\Component\Log\Monolog;

/**
 * Class PdoDB
 * @deprecated use MedooDB instead
 * @package Flight2wwu\Component\Database
 */
class PdoDB implements ServiceProvider
{

    /**
     * @var \PDO
     */
    private $db;

    /**
     * @var array
     */
    private $lastError;

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

    function __construct()
    {

    }

    /**
     * @return \PDO
     */
    public function getDB()
    {
        return $this->db;
    }

    /**
     * @param string $name
     */
    public function reconnect($name = 'main')
    {
        $dbconfig = \Flight::get('database');
        if (!$dbconfig || !is_array($dbconfig)) {
            $this->logError(['error'=>'Database is invalid', 'code'=>1]);
            return;
        }
        $dbfield = array_key_exists($name, $dbconfig) ? $dbconfig[$name] : $dbconfig['main'];
        $this->connect($dbfield);
    }

    /**
     * @param array $dbconfig
     */
    public function connect(array $dbconfig)
    {
        $driver = $dbconfig['driver'];
        $host = $dbconfig['host'];
        $port = $dbconfig['port'];
        $dbname = $dbconfig['dbname'];
        $user = $dbconfig['user'];
        $password = $dbconfig['password'];
        try {
            $this->db = new \PDO("$driver:host=$host;port=$port;dbname=$dbname", $user, $password);
        } catch (\PDOException $e) {
            $this->logError(['message'=>$e->getMessage(), 'code'=>2]);
        }
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
     * Prepare query.
     *
     * @param $query
     * @return \PDOStatement
     */
    public function prepare($query)
    {
        $query = str_replace(';', ' ', $query);
        $statement = $this->db->prepare($query);
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
            $this->logDebug($statement->queryString, $data);
            if (!$re) {
                $this->setError($statement->errorInfo());
                $this->logError($this->lastError, $statement->queryString);
                return false;
            }
            return $statement;
        } catch (\Exception $e) {
            $this->setError($statement->errorInfo());
            $this->logError($this->lastError, $statement->queryString);
            return false;
        }
    }

    /**
     * @return array
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * start transaction
     *
     * @return bool
     */
    public function begin()
    {
        return $this->db->beginTransaction();
    }

    /**
     * rollback transaction
     *
     * @return bool
     */
    public function rolback()
    {
        return $this->db->rollBack();
    }

    /**
     * commit transaction
     *
     * @return bool
     */
    public function commit()
    {
        return $this->db->commit();
    }

    /**
     * @var array
     */
    private static $operators = [
        'eq'=>'=', 'ne'=>'!=', 'gt'=>'>', 'ge'=>'>=', 'lt'=>'<', 'le'=>'<=',
        'like'=>'like', 'ilike'=>'ilike', 'nlike'=>'not like', 'nilike'=>'not ilike',
        'is'=>'is', 'isn'=>'is not', 'in'=>'in', 'nin'=>'not in', 're'=>'~*'
    ];

    /**
     * Convert where params to where sql.
     * [field.operator.value] or [field#.operator.value]
     *
     * @param string $params
     * @param string $sep
     * @return string
     */
    public function formatWhereParams($params, $sep = '.')
    {
        $wh = preg_replace_callback('/\[.+?\]/', function($match) use ($sep) {
            $stack = [];
            $field = '';
            $operator = '';
            $value = '';
            $noquote = false;
            $m = substr($match[0], 1, strlen($match[0]) - 2);
            for ($i = 0; $i < strlen($m); $i++) {
                $c = substr($m, $i, 1);
                if ($c != $sep) {
                    array_push($stack, $c);
                } else {
                    $str = implode('', $stack);
                    $stack = [];
                    if (!$field) {
                        $str = trim(str_replace(';', '', $str));
                        if (substr($str, strlen($str) - 1, 1) == '#') {
                            $noquote = true;
                            $str = substr($str, 0, strlen($str) - 1);
                        }
                        $field = $str;
                    } elseif (!$operator) {
                        if (array_key_exists($str, PdoDB::$operators)) {
                            $operator = PdoDB::$operators[$str];
                            break;
                        } else {
                            return '';
                        }
                    }
                }
            }
            if (count($stack) > 0) {
                return '';
            }
            $value = substr($m, $i + 1);
            if (strtolower($value) == 'null') {
                if ($operator == 'is' || $operator == 'is not') {
                    $value = 'null';
                } else {
                    $value = $this->db->quote($value);
                }
            } else {
                if (!$noquote) {
                    $value = $this->db->quote($value);
                }
            }
            return "$field $operator $value";
        }, $params);
        return $wh;
    }

    /**
     * Convert where array to string.
     * [[field, operator, value, <type>], ...]
     *
     * @param array $where
     * @param bool $and
     * @return string
     */
    public function formatWhereArray(array $where, $and = true)
    {
        $out = [];
        foreach ($where as $w) {
            if (is_array($w) && count($w) > 2) {
                $field = $w[0];
                $operator = $w[1];
                if (array_search($operator, PdoDB::$operators) === false) {
                    continue;
                }
                $value = $w[2];
                if (is_null($value)) {
                    if ($operator == 'is' || $operator == 'is not') {
                        $value = 'null';
                    } else {
                        continue;
                    }
                } else {
                    if (count($w) > 3) {
                        $type = strtolower($w[3]);
                        if ($type != 'null' && $type != 'integer' && $type != 'double') {
                            $value = $this->db->quote($value);
                        }
                    } else {
                        $type = strtolower(gettype($value));
                        if ($type != 'null' && $type != 'integer' && $type != 'double') {
                            $value = $this->db->quote($value);
                        }
                    }
                }
                array_push($out, "$field $operator $value");
            }
        }
        if ($and) {
            $sep = ' and ';
        } else {
            $sep = ' or ';
        }
        return implode($sep, $out);
    }

    /**
     * @param \PDOStatement $statement
     * @param array $data  [key1=>[value1, type1], key1=>value2, ...]
     */
    private function bindValue($statement, array $data)
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
     */
    private function setError(array $error)
    {
        $this->lastError = ['code'=>$error[0], 'message'=>$error[2]];
    }

    /**
     * @param array $error
     * @param string $sql
     */
    private function logError(array $error, $sql = '')
    {
        $logger = getLog();
        $logger->changeLogger('database')->error("Code (" . $error['code'] . ') ' . $error['message'] . " by $sql");
        $logger->changeLogger('main');
    }

    /**
     * @param string $sql
     * @param array $data
     */
    private function logDebug($sql = '', array $data = [])
    {
        $logger = getLog();
        $logger->changeLogger('database')->debug("Execute $sql", $data);
        $logger->changeLogger('main');
    }

} 