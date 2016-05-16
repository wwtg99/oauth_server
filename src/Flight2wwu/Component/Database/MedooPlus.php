<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2016/5/7 0007
 * Time: 下午 11:44
 */

namespace Flight2wwu\Component\Database;


class MedooPlus extends \medoo
{
    /**
     * @var bool
     */
    public $debug = false;

    /**
     * @var array
     */
    protected $lastError = [];

    /**
     * @var string
     */
    protected $lastSql = '';

    /**
     * MedooPlus constructor.
     * @param array $options
     */
    public function __construct($options)
    {
        parent::__construct($options);
    }

    /**
     * @param string $table
     * @param array $join
     * @param string|array $columns
     * @param array $where
     * @return array|bool
     *
     * Or,
     * @param string $table
     * @param string|array $columns
     * @param array $where
     * @return array|bool
     */
    public function select($table, $join, $columns = null, $where = null)
    {
        $re = parent::select($table, $join, $columns, $where);
        $this->lastSql = $this->last_query();
        $this->lastError = $this->error();
        if ($re === false) {
            $this->logError();
        }
        $this->logQuery();
        return $re;
    }

    /**
     * @param string $table
     * @param array $datas
     * @return bool|int
     */
    public function insert($table, $datas)
    {
        // Check indexed or associative array
        if (!isset($datas[ 0 ])) {
            $datas = array($datas);
        }
        $n = 0;
        foreach ($datas as $data) {
            $values = array();
            $columns = array();
            foreach ($data as $key => $value) {
                array_push($columns, $this->column_quote($key));
                switch (gettype($value)) {
                    case 'NULL':
                        $values[] = 'NULL';
                        break;

                    case 'array':
                        preg_match("/\(JSON\)\s*([\w]+)/i", $key, $column_match);

                        $values[] = isset($column_match[ 0 ]) ?
                            $this->quote(json_encode($value)) :
                            $this->quote(serialize($value));
                        break;

                    case 'boolean':
                        $values[] = ($value ? '1' : '0');
                        break;

                    case 'integer':
                    case 'double':
                    case 'string':
                        $values[] = $this->fn_quote($key, $value);
                        break;
                }
            }
            $re = $this->exec('INSERT INTO "' . $this->prefix . $table . '" (' . implode(', ', $columns) . ') VALUES (' . implode($values, ', ') . ')');
            $this->lastSql = $this->last_query();
            $this->lastError = $this->error();
            if ($re === false) {
                $this->logError();
            }
            $this->logQuery();
            $n += $re;
        }
        return $n;
    }

    /**
     * @param string $table
     * @param array $data
     * @param array $where
     * @return bool|int
     */
    public function update($table, $data, $where = null)
    {
        $re = parent::update($table, $data, $where);
        $this->lastSql = $this->last_query();
        $this->lastError = $this->error();
        if ($re === false) {
            $this->logError();
        }
        $this->logQuery();
        return $re;
    }

    /**
     * @param string $table
     * @param array $where
     * @return bool|int
     */
    public function delete($table, $where)
    {
        $re = parent::delete($table, $where);
        $this->lastSql = $this->last_query();
        $this->lastError = $this->error();
        if ($re === false) {
            $this->logError();
        }
        $this->logQuery();
        return $re;
    }

    /**
     * @param string $table
     * @param array $join
     * @param string|array $column
     * @param array $where
     * @return bool|array
     *
     * Or,
     * @param string $table
     * @param string|array $column
     * @param array $where
     * @return bool|array
     */
    public function get($table, $join = null, $column = null, $where = null)
    {
        $re = parent::get($table, $join, $column, $where);
        $this->lastSql = $this->last_query();
        $this->lastError = $this->error();
        if ($re === false) {
            $this->logError();
        }
        $this->logQuery();
        return $re;
    }

    /**
     * @param string $table
     * @param array $join
     * @param array $where
     * @return bool
     *
     * Or,
     * @param string $table
     * @param array $where
     * @return bool
     */
    public function has($table, $join, $where = null)
    {
        $column = null;
        $re = $this->query('SELECT EXISTS(' . $this->select_context($table, $join, $column, $where, 1) . ')');
        $this->lastSql = $this->last_query();
        $this->lastError = $this->error();
        if ($re === false) {
            $this->logError();
        }
        $this->logQuery();
        if ($re) {
            return $re->fetchColumn();
        } else {
            return false;
        }
    }

    /**
     * @param string $table
     * @param array $join
     * @param string|array $column
     * @param array $where
     * @return bool|int
     *
     * Or,
     * @param string $table
     * @param array $where
     * @return bool|int
     */
    public function count($table, $join = null, $column = null, $where = null)
    {
        $re = parent::count($table, $join, $column, $where);
        $this->lastSql = $this->last_query();
        $this->lastError = $this->error();
        if ($re === false) {
            $this->logError();
        }
        $this->logQuery();
        return $re;
    }

    /**
     * @return bool
     */
    public function begin()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * @return bool
     */
    public function rollback()
    {
        return $this->pdo->rollBack();
    }

    /**
     * @return bool
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * @param string $query
     * @param array $data
     * @return array|bool
     */
    public function queryAll($query, $data = [])
    {
        $stmt = $this->prepare($query);
        $stmt = $this->executeStatement($stmt, $data);
        if ($stmt) {
            return $stmt->fetchAll();
        }
        return false;
    }

    /**
     * @param string $query
     * @param array $data
     * @return array|bool
     */
    public function queryOne($query, $data = [])
    {
        $stmt = $this->prepare($query);
        $stmt = $this->executeStatement($stmt, $data);
        if ($stmt) {
            return $stmt->fetch();
        }
        return false;
    }

    /**
     * @param string $query
     * @param array $data
     * @return int|bool
     */
    public function execute($query, $data = [])
    {
        $stmt = $this->prepare($query);
        $stmt = $this->executeStatement($stmt, $data);
        if ($stmt) {
            return $stmt->rowCount();
        }
        return false;
    }

    /**
     * @param string $query
     * @return \PDOStatement
     */
    public function prepare($query)
    {
        $query = str_replace(';', ' ', $query);
        $statement = $this->pdo->prepare($query);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);
        return $statement;
    }

    /**
     * @param \PDOStatement $statement
     * @param array $data
     * @return bool|\PDOStatement
     * @throws \Exception
     */
    public function executeStatement(\PDOStatement $statement, array $data = array())
    {
        $this->bindValue($statement, $data);
        try {
            $re = $statement->execute();
            $this->lastSql = $statement->queryString;
            $this->lastError = $statement->errorInfo();
            if (!$re) {
                $this->logError();
                return false;
            }
            $this->logQuery();
            return $statement;
        } catch (\Exception $e) {
            $this->logError($e->getMessage());
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
     * @return string
     */
    public function getLastSql()
    {
        return $this->lastSql;
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
     * Log sql query if debug is true.
     */
    private function logQuery()
    {
        if ($this->debug) {
            $l = getLog();
            $l->changeLogger('database');
            $l->debug($this->lastSql);
            $l->changeLogger('main');
        }
    }

    /**
     * @param string $msg
     */
    private function logError($msg = '')
    {
        $l = getLog();
        $l->error('Query Error for ' . $this->lastSql . " $msg", $this->lastError);
    }

}