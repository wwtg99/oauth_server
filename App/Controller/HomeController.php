<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/10
 * Time: 18:04
 */

namespace App\Controller;

use DebugBar\StandardDebugBar;
use Flight2wwu\Common\BaseController;
use Flight2wwu\Component\Auth\RoleAuth;
use Flight2wwu\Component\Log\Monolog;

class HomeController extends BaseController
{
    /**
     * Home page
     */
    public static function home()
    {
        getView()->render('home');
    }

    /**
     * Switch language
     * @return bool
     */
    public static function language()
    {
        $locale = self::getInput('language');
        if ($locale) {
            \Flight::Locale()->setLocale($locale);
            getOValue()->addOld('language', $locale);
        }
        return true;
    }

    /**
     * Role based access control and access log
     * @return bool
     */
    public static function rbac()
    {
        $ip = self::getRequest()->ip;
        $url = self::getRequest()->url;
        $method = self::getRequest()->method;
        $path = parse_url($url, PHP_URL_PATH);
        if (getAuth()->isLogin()) {
            $user = getUser()['user_id'];
        } else {
            $user = 'anonymous';
        }
        $logger = getLog();
        //skip /403
        if ($path == '/403') {
            return true;
        }
        // log access
        if (!getAuth()->accessPath($path)->access(self::getRequest()->method)) {
            $logger->changeLogger('access')->info("forbidden for $path by $user");
            $logger->changeLogger('main');
            \Flight::redirect('/403');
            return false;
        } else {
            $logger->changeLogger('access')->info("Access from $ip by $user for $url method $method");
            $logger->changeLogger('main');
        }
        return true;
    }

    /**
     * Forbidden, error 403
     * @return bool
     */
    public static function forbidden()
    {
        if (self::getRequest()->ajax) {
            \Flight::json([]);
        } else {
            getView()->render('error/403');
        }
        return false;
    }

    /**
     * Change log
     */
    public static function changelog()
    {
        self::defaultHeader();
        $md = new \Parsedown();
        $f = file_get_contents(WEB . 'changelog.txt');
        echo $md->text($f);
    }
} 