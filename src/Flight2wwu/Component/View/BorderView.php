<?php
/**
 * Created by PhpStorm.
 * User: wwt
 * Date: 2015/12/17 0017
 * Time: 下午 8:55
 */

namespace Flight2wwu\Component\View;


class BorderView extends AbstractView
{
    /**
     * @var string
     */
    private $head = '';

    /**
     * @var string
     */
    private $foot = '';

    /**
     * @var string
     */
    private $left = '';

    /**
     * @var string
     */
    private $right = '';

    /**
     * @var string
     */
    private $center = '';

    /**
     * @var string
     */
    private $layoutTemplate = '';

    /**
     * Called after all class is registered.
     *
     * @return void
     */
    public function boot()
    {
        $this->head = 'border_head';
        $this->foot = 'border_foot';
        $this->left = 'border_left';
        $this->right = 'border_right';
        $this->center = 'border_center';
        $this->layoutTemplate = 'border_layout';
    }

    /**
     * @param array|string $template
     * @param array $data
     */
    public function render($template, array $data = null)
    {
        if (is_array($template)) {
            if (array_key_exists('head', $template)) {
                $this->setHead($template['head']);
            }
            if (array_key_exists('foot', $template)) {
                $this->setFoot($template['foot']);
            }
            if (array_key_exists('left', $template)) {
                $this->setLeft($template['left']);
            }
            if (array_key_exists('right', $template)) {
                $this->setRight($template['right']);
            }
            if (array_key_exists('center', $template)) {
                $this->setCenter($template['center']);
            }
            if (array_key_exists('layout', $template)) {
                $this->setLayoutTemplate($template['layout']);
            }
        } else {
            $this->setCenter($template);
        }
        $this->renderTemplate($this->head, $data, 'head');
        $this->renderTemplate($this->foot, $data, 'foot');
        $this->renderTemplate($this->right, $data, 'right');
        $this->renderTemplate($this->left, $data, 'left');
        $this->renderTemplate($this->center, $data, 'center');
        $this->renderTemplate($this->layoutTemplate, $data);
    }

    /**
     * @return string
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * @param string $head
     */
    public function setHead($head)
    {
        $this->head = $head;
    }

    /**
     * @return string
     */
    public function getFoot()
    {
        return $this->foot;
    }

    /**
     * @param string $foot
     */
    public function setFoot($foot)
    {
        $this->foot = $foot;
    }

    /**
     * @return string
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param string $left
     */
    public function setLeft($left)
    {
        $this->left = $left;
    }

    /**
     * @return string
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param string $right
     */
    public function setRight($right)
    {
        $this->right = $right;
    }

    /**
     * @return string
     */
    public function getCenter()
    {
        return $this->center;
    }

    /**
     * @param string $center
     */
    public function setCenter($center)
    {
        $this->center = $center;
    }

    /**
     * @return string
     */
    public function getLayoutTemplate()
    {
        return $this->layoutTemplate;
    }

    /**
     * @param string $layoutTemplate
     */
    public function setLayoutTemplate($layoutTemplate)
    {
        $this->layoutTemplate = $layoutTemplate;
    }

    /**
     * @param string $template
     * @return array
     */
    private function getTemplate($template)
    {
        $template = trim($template);
        $dpos = strrpos($template, DIRECTORY_SEPARATOR);
        if ($dpos !== false) {
            $tpath = substr($template, 0, $dpos);
            $tfile = substr($template, $dpos + 1);
        } else {
            $tpath = '';
            $tfile = $template;
        }
        return [$tpath, $tfile];
    }

    /**
     * @param string $template
     * @param array $data
     * @param string $key
     */
    private function renderTemplate($template, $data = null, $key = null)
    {
        if (!$template) {
            return;
        }
        $temp = $this->getTemplate($template);
        if ($temp[0]) {
            \Flight::view()->path = VIEW . $temp[0];
            \Flight::render($temp[1], $data, $key);
            \Flight::view()->path = VIEW;
        } else {
            \Flight::render($temp[1], $data, $key);
        }
    }

}