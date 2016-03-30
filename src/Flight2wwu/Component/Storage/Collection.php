<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/3/14
 * Time: 10:47
 */

namespace Flight2wwu\Component\Storage;


class Collection implements IAttribute, \Iterator
{

    /**
     * @var string|array
     */
    private $content;

    /**
     * Collection constructor.
     * @param $content
     */
    public function __construct($content)
    {
        if ($content instanceof Collection) {
            $this->content = $content->getContent();
        } else {
            $this->content = $content;
        }
    }

    /**
     * @return array|string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param array|string $content
     * @return Collection
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        if (is_array($this->content)) {
            return array_key_exists($name, $this->content);
        }
        return false;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name = null)
    {
        if (is_null($name)) {
            return $this->content;
        }
        if ($this->has($name)) {
            return $this->content[$name];
        }
        return null;
    }

    /**
     * @param string $name
     * @param $val
     * @param int $expires: not supported
     * @return IAttribute
     */
    public function set($name, $val, $expires = 0)
    {
        if (is_null($name)) {
            $this->content = $val;
            return $this;
        }
        if (is_array($this->content)) {
            $this->content[$name] = $val;
        } else {
            $this->content = $val;
        }
        return $this;
    }

    /**
     * @param string $name
     * @return IAttribute
     */
    public function delete($name)
    {
        if ($this->has($name)) {
            unset($this->content[$name]);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function current()
    {
        if (is_array($this->content)) {
            return current($this->content);
        } else {
            return $this->content;
        }
    }

    /**
     * @return mixed
     */
    public function next()
    {
        if (is_array($this->content)) {
            return next($this->content);
        } else {
            return 0;
        }
    }

    /**
     * @return mixed
     */
    public function key()
    {
        if (is_array($this->content)) {
            return key($this->content);
        } else {
            return 0;
        }
    }

    /**
     * @return mixed
     */
    public function valid()
    {
        if (is_array($this->content)) {
            return current($this->content) != false;
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function rewind()
    {
        if (is_array($this->content)) {
            return reset($this->content);
        } else {
            return $this->content;
        }
    }

}