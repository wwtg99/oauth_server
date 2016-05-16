<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/10
 * Time: 18:04
 */

namespace App\Controller;

use Flight2wwu\Common\BaseController;
use Flight2wwu\Component\Utils\FormatUtils;

class HomeController extends BaseController
{
    /**
     * Home page
     */
    public static function home()
    {
        getAssets()->addCss('home.css', '/asserts/custom');
        getView()->render(['center'=>'home', 'foot'=>'']);
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
        } else {
            $locale = getOValue()->getOld('language');
            if ($locale) {
                \Flight::Locale()->setLocale($locale);
            }
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
        // last path
        $skip = ['/403', '/404', '/auth/login', '/oauth/login', '/oauth/redirect_login'];
        if (!in_array($path, $skip) && !self::getRequest()->ajax) {
            getOValue()->addOldOnce('last_path', $path);
        }
        if (getAuth()->isLogin()) {
            $user = getUser()['user_id'];
        } else {
            $user = 'anonymous';
        }
        $logger = getLog();
        // skip /403
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
            getView()->render('error/403', ['title'=>'authentication failed']);
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

    public static function apis()
    {
        $api_head = ['uri', 'descr', 'need_scope', 'param'];
        $apis = [
            ['uri'=>'/user/info', 'descr'=>'获取用户信息', 'need_scope'=>'get_user_info', 'param'=>null],
            ['uri'=>'/tool/send_email', 'descr'=>'以no-reply@genowise.com发送邮件', 'need_scope'=>'send_email', 'param'=>'<ul><li>to: 收件人，多个以逗号分隔，必选</li><li>subject: 主题，必选</li><li>body: 正文，支持部分html代码，必选</li><li>cc: 抄送，可选</li><li>bcc: 密送，可选</li></ul>'],
            ['uri'=>'/tool/express_info', 'descr'=>'查询快递信息', 'need_scope'=>T('open for all'), 'param'=>'<ul><li>company: 快递公司代号，详见<a href="/express_company.txt" target="_blank">列表</a>，必选</li><li>no: 快递单号，必选</li><li>type: 返回类型，默认字符串，可选json</li></ul>'],
        ];
        getAssets()->addLibrary(['bootstrap-table']);
        getView()->render('apis', ['apis'=>$apis, 'api_head'=>FormatUtils::formatHead($api_head)]);
    }

    public static function document()
    {
        $py_readme = file_get_contents(WEB . implode(DIRECTORY_SEPARATOR, ['sdk', 'python', 'README.md']));
        $php_readme = file_get_contents(WEB . implode(DIRECTORY_SEPARATOR, ['sdk', 'php', 'README.md']));
        $readme = ['python'=>$py_readme, 'php'=>$php_readme];
        getView()->render('documents', ['readme'=>$readme]);
    }
} 