<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/19
 * Time: 15:09
 */

namespace Flight2wwu\Component\Utils;

use Flight2wwu\Common\ServiceProvider;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class Express
 * Get express package info from http://www.kiees.cn/
 * @package Flight2wwu\Component\Utils
 */
class Express implements ServiceProvider
{

    /**
     * @var array
     */
    private $request_url = [];

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
        $this->loadConfig(\Flight::get('express'));
    }

    /**
     * @param string $company
     * @param string $no
     * @return array
     */
    public function track($company, $no)
    {
        if (array_key_exists($company, $this->request_url)) {
            $url = $this->request_url[$company]['url'];
            $url = str_replace('<no>', trim($no), $url);
            $client = new Client();
            $res = $client->get($url);
            echo $res->getBody();
            $arr = $this->extract($company, $res->getBody()->getContents());
            return $arr;
        } else {
            return [];
        }
    }

    /**
     * @param string $company
     * @param string $no
     * @return string
     */
    public function current($company, $no)
    {
        $arr = Express::track($company, $no);
        if ($arr) {
            return $arr[count($arr) - 1];
        }
        return '';
    }

    /**
     * @param array $arr
     */
    public function loadConfig(array $arr)
    {
        foreach ($arr as $a) {
            $name = $a[0];
            $chname = $a[1];
            $url = $a[2];
            $func = $a[3];
            $this->request_url[$name] = ['label'=>$chname, 'url'=>$url, 'func'=>$func];
        }
    }

    /**
     * @param string $company
     * @param string $html
     * @return array
     */
    private function extract($company, $html)
    {
        $arr = $this->request_url[$company];
        $func = $arr['func'];
        $rm = new \ReflectionMethod($this, $func);
        return $rm->invoke($this, $html);
    }

    /**
     * Extract from kiees
     *
     * @param string $html
     * @return array
     */
    public function extractKieesTable($html)
    {
        $crawler = new Crawler();
        $crawler->addHtmlContent($html, 'UTF-8');
        $arr = $crawler->filter('table tr td')->each(function($node, $i) {
            return $node->text();
        });
        //remove ad
        if ($arr) {
            $last = $arr[count($arr) - 1];
            $arr[count($arr) - 1] = substr($last, 0, strpos($last, '|'));
        }
        return $arr;
    }
} 