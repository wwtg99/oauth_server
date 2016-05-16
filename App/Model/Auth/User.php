<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/10/8
 * Time: 10:47
 */

namespace App\Model\Auth;

/**
 * Class User
 * @package wwu\App\Model
 */
class User
{

    use TNormalAuth;
//    use TOpenAuth; //should disable admin departments/users/roles, change password and edit user info

    const KEY_USER_ID = 'user_id';
    const KEY_USER_NAME = 'name';
    const KEY_USER_PASSWORD = 'password';
    const KEY_USER_EMAIL = 'email';
    const KEY_USER_TOKEN = 'remember_token';
    const TABLE_USER = 'users';
    const VIEW_USER = 'view_users';

    public static $head = '*';

} 