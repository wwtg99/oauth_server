<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/4/6
 * Time: 10:47
 */

namespace App\Model\Orm;


use Flight2wwu\Component\Database\OrmModel;

class Users extends OrmModel
{

    protected $tableName = 'users';
    protected $tableKey = 'user_id';
}