<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/10/8
 * Time: 15:09
 */

namespace Flight2wwu\Component\Database;

class Pagination
{

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    public static $pageSize = 100;

    const PAG_KEY = 'pagination';

    /**
     * @param int $limit
     * @param int $offset
     */
    function __construct($limit = 1000, $offset = 0)
    {
        if ($limit <= 0) {
            $limit = 1000;
        }
        if ($offset <= 0) {
            $offset = 0;
        }
        $this->limit = $limit;
        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @param int $num
     * @return $this
     */
    public function next($num)
    {
        $this->offset += $num;
        return $this;
    }

    /**
     * @return int
     */
    public function total()
    {
        return $this->limit + $this->offset;
    }

    /**
     * @param int $start
     * @param int $end
     * @param int $size
     * @return Pagination
     */
    public static function fromPage($start, $end, $size)
    {
        if (is_null($start) || is_null($end) || is_null($size)) {
            return null;
        }
        $limit = ($end - $start + 1) * $size;
        $offset = ($start - 1) * $size;
        return new Pagination($limit, $offset);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return Pagination
     */
    public static function fromLimit($limit, $offset)
    {
        if (is_null($limit) || is_null($offset)) {
            return null;
        }
        return new Pagination($limit, $offset);
    }

    /**
     * @param $name
     * @return Pagination
     */
    public static function getAutoPage($name)
    {
        $pages = getOValue()->getOld(Pagination::PAG_KEY);
        if ($pages && array_key_exists($name, $pages)) {
            $page = & $pages[$name];
            $page->next(Pagination::$pageSize);
            return $page;
        }
        $pag = new Pagination(self::$pageSize, 0);
        getOValue()->addOld(Pagination::PAG_KEY, [$name => $pag]);
        return $pag;
    }

    /**
     * @param string $name
     */
    public static function clearPage($name = '')
    {
        if ($name) {
            $p = getOValue()->getOld(Pagination::PAG_KEY, null);
            if ($p && array_key_exists($name, $p)) {
                unset($p[$name]);
                getOValue()->addOld(Pagination::PAG_KEY, $p);
            }
        } else {
            getOValue()->deleteOld(Pagination::PAG_KEY);
        }
    }

} 