<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/4/6
 * Time: 10:25
 */

namespace Flight2wwu\Component\Database;


abstract class OrmModel
{

    /**
     * @var string
     */
    protected $tableName = '';

    /**
     * @var string|array
     */
    protected $tableKey = '';

    /**
     * @param string|array $key
     * @param string|array $select
     * @return array
     * @throws \Exception
     */
    public function show($key, $select = null)
    {
        $where = $this->formatKey($key);
        if (is_null($select)) {
            $select = '*';
        }
        $db = getDB();
        $re = $db->getConnection()->get($this->getTableName(), $select, $where);
        if ($re) {
            return $re;
        }
        return [];
    }

    /**
     * @param string|array $select
     * @param array|null $where
     * @param bool|Pagination $page: true for auto page, false for new auto page, null for all
     * @return array
     * @throws \Exception
     */
    public function lists($select = null, $where = null, $page = null)
    {
        $db = getDB();
        if (is_null($select)) {
            $select = '*';
        }
        if ($page === true) {
            $page = Pagination::getAutoPage($this->getTableName());
        } elseif ($page === false) {
            Pagination::clearPage($this->getTableName());
            $page = Pagination::getAutoPage($this->getTableName());
        }
        if ($page instanceof Pagination) {
            $where['LIMIT'] = [$page->getOffset(), $page->getLimit()];
        }
        $re = $db->getConnection()->select($this->getTableName(), $select, $where);
        if ($re) {
            return $re;
        }
        return [];
    }

    /**
     * @param array|null $where
     * @return int
     * @throws \Exception
     */
    public function count($where = null)
    {
        $db = getDB();
        $re = $db->getConnection()->count($this->getTableName(), $where);
        return $re;
    }

    /**
     * @param array $data
     * @param array $where
     * @return bool|int
     * @throws \Exception
     */
    public function update(array $data, array $where)
    {
        $db = getDB();
        $re = $db->getConnection()->update($this->getTableName(), $data, $where);
        return $re;
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function insert(array $data)
    {
        $db = getDB();
        $re = $db->getConnection()->insert($this->getTableName(), $data);
        return $re;
    }

    /**
     * @param string|array $key
     * @return bool|int
     * @throws \Exception
     */
    public function delete($key)
    {
        $where = $this->formatKey($key);
        $db = getDB();
        $re = $db->getConnection()->delete($this->getTableName(), $where);
        return $re;
    }

    /**
     * @param string|array $text
     * @param string|array $fields
     * @param string|array $select
     * @param bool|Pagination $page: true for auto page, false for new auto page, null for all
     * @return array|bool
     * @throws \Exception
     */
    public function search($text, $fields = [], $select = null, $page = null)
    {
        if (is_null($select)) {
            $select = '*';
        }
        if (!$fields) {
            $fields = $this->getTableKey();
        }
        if (!$fields) {
            return [];
        }
        if (is_array($fields)) {
            $where = ['OR'=>[]];
            foreach ($fields as $field) {
                $where['OR'][$field . '[~]'] = $text;
            }
        } else {
            $f = $fields . '[~]';
            $where = ['OR'=>[$f => $text]];
        }
        if ($page === true) {
            $page = Pagination::getAutoPage($this->getTableName() . '_search');
        } elseif ($page === false) {
            Pagination::clearPage($this->getTableName() . '_search');
            $page = Pagination::getAutoPage($this->getTableName() . '_search');
        }
        if ($page instanceof Pagination) {
            $where['LIMIT'] = [$page->getOffset(), $page->getLimit()];
        }
        $db = getDB();
        $re = $db->getConnection()->select($this->getTableName(), $select, $where);
        if ($re) {
            return $re;
        }
        return [];
    }

    /**
     * @return string
     */
    protected function getTableName()
    {
        if (property_exists($this, 'tableName') && $this->tableName) {
            return $this->tableName;
        }
        $clsname = get_class($this);
        $sindex = strrpos($clsname, '\\');
        if ($sindex !== false) {
            $clsname = substr($clsname, $sindex + 1);
        }
        return strtolower($clsname);
    }

    /**
     * @return array|string
     */
    protected function getTableKey()
    {
        if (property_exists($this, 'tableKey') && $this->tableKey) {
            return $this->tableKey;
        }
        return '';
    }

    /**
     * @param string|array $key
     * @return array|null
     */
    protected function formatKey($key)
    {
        $tkey = $this->getTableKey();
        if (!$tkey) {
            return null;
        } elseif (is_array($tkey) && is_array($key)) {
            foreach ($tkey as $item) {
                if (!array_key_exists($item, $key)) {
                    return null;
                }
            }
            return ['AND' => $key];
        } elseif (is_string($tkey) && is_string($key)) {
            return ['AND' => [$tkey => $key]];
        } else {
            return null;
        }
    }
}