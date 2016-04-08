<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/2/16
 * Time: 15:05
 */

namespace App\Model;


class Admin
{

    /**
     * @param $user_id
     * @return array
     */
    public static function getUser($user_id = null)
    {
        $db = getDB();
        if (is_null($user_id)) {
            $re = $db->getConnection()->select('view_users',
                ['user_id', 'name', 'label', 'email', 'descr', 'department_id', 'department', 'department_descr', 'superuser', 'roles', 'active', 'created_at']);
            getLog()->warning($db->getConnection()->last_query());
        } else {
            $re = $db->getConnection()->get('view_users',
                ['user_id', 'name', 'label', 'email', 'descr', 'department_id', 'department', 'department_descr', 'superuser', 'roles', 'active', 'created_at'],
                ['AND' => ['user_id' => $user_id]]);
        }
        if ($re) {
            return $re;
        }
        return [];
    }

    /**
     * @param $department_id
     * @return array
     * @throws \Exception
     */
    public static function getDepartment($department_id = null)
    {
        $db = getDB();
        if (is_null($department_id)) {
            $re = $db->getConnection()->select('departments', ['department_id', 'name', 'descr', 'created_at'], ['deleted_at' => null]);
        } else {
            $re = $db->getConnection()->get('departments', ['department_id', 'name', 'descr', 'created_at'], ['AND' => ['department_id' => $department_id, 'deleted_at' => null]]);
        }
        if ($re) {
            return $re;
        }
        return [];
    }

    /**
     * @param $role_id
     * @return array
     * @throws \Exception
     */
    public static function getRoles($role_id = null)
    {
        $db = getDB();
        if (is_null($role_id)) {
            $re = $db->getConnection()->select('roles', ['role_id', 'name', 'descr', 'created_at'], ['deleted_at' => null]);
        } else {
            $re = $db->getConnection()->get('roles', ['role_id', 'name', 'descr', 'created_at'], ['AND' => ['role_id' => $role_id, 'deleted_at' => null]]);
        }
        if ($re) {
            return $re;
        }
        return [];
    }

    /**
     * @param $user_id
     * @param $roles
     * @return bool
     */
    public static function changeRoles($user_id, $roles)
    {
        $db = getDB();
        if (is_array($roles)) {
            $roles = implode(',', $roles);
        }
        $re = $db->queryOne("select change_roles(:uid, :role)", ['uid' => $user_id, 'role' => $roles]);
        if ($re) {
            return true;
        }
        return false;
    }

    /**
     * @param $user_id
     * @return bool
     */
    public static function resetPassword($user_id)
    {

    }

    /**
     * @param $user_id
     * @param array $user
     * @param $roles
     * @return string|bool
     */
    public static function updateUser($user_id, array $user, $roles)
    {
        $db = getDB();
        $db->exec('BEGIN;');
        $re = $db->queryOne("select update_user(:uid, :user)", ['uid' => $user_id, 'user' => json_encode($user)]);
        if ($re) {
            $re = Admin::changeRoles($user_id, $roles);
            if ($re) {
                $db->exec('COMMIT;');
                return $user_id;
            }
        }
        $db->exec('ROLLBACK;');
        return false;
    }

    /**
     * @param $name
     * @param $department_id
     * @param $roles
     * @param $label
     * @param $email
     * @param $descr
     * @return string|bool
     */
    public static function createUser($name, $department_id, $roles, $label = null, $email = null, $descr = null)
    {
        $db = getDB();
        $db->exec('BEGIN;');
        $re = $db->queryOne("select create_user(:name, :dep, :lab, :eml, :des)",
            ['name'=>$name, 'dep'=>$department_id, 'lab'=>$label, 'eml'=>$email, 'des'=>$descr]);
        if ($re && $re['create_user']) {
            $uid = $re['create_user'];
            $re = Admin::changeRoles($uid, $roles);
            if ($re) {
                $db->exec('COMMIT;');
                return $uid;
            }
        }
        $db->exec('ROLLBACK;');
        return false;
    }

    /**
     * @param $user_id
     * @return bool
     */
    public static function deleteUser($user_id)
    {
        $db = getDB();
        $re = $db->queryOne("select delete_user(:uid)", ['uid'=>$user_id]);
        if ($re) {
            return true;
        }
        return false;
    }

    /**
     * @param $user_id
     * @param bool $active
     * @return bool
     * @throws \Exception
     */
    public static function activeUser($user_id, $active = true)
    {
        $db = getDB();
        $re = $db->getConnection()->update('users', ['active'=>$active], ['user_id'=>$user_id]);
        if ($re) {
            return true;
        }
        return false;
    }

    /**
     * @param $name
     * @param $descr
     * @return bool
     */
    public static function createRole($name, $descr)
    {
        $db = getDB();
        $re = $db->queryOne("select create_role(:name, :des)", ['name'=>$name, 'des'=>$descr]);
        if ($re && $re['create_role']) {
            return $re['create_role'];
        }
        return false;
    }

    /**
     * @param $role_id
     * @return bool
     */
    public static function deleteRole($role_id)
    {
        //system roles
        if ($role_id == 1 || $role_id == 2) {
            return false;
        }
        $db = getDB();
        $re = $db->queryOne("select delete_role(:rid)", ['rid'=>$role_id]);
        if ($re) {
            return true;
        }
        return false;
    }

    /**
     * @param $role_id
     * @param array $role
     * @return bool
     */
    public static function updateRole($role_id, array $role)
    {
        //system roles
        if ($role_id == 1 || $role_id == 2) {
            return false;
        }
        $db = getDB();
        $re = $db->queryOne("select update_role(:rid, :role)", ['rid' => $role_id, 'role' => json_encode($role)]);
        if ($re) {
            return true;
        }
        return false;
    }

    /**
     * @param $department_id
     * @param $name
     * @param $descr
     * @return bool
     * @throws \Exception
     */
    public static function createDepartment($department_id, $name, $descr)
    {
        $db = getDB();
        $db->getConnection()->insert('departments', ['department_id'=>$department_id, 'name'=>$name, 'descr'=>$descr]);
        $re = self::getDepartment($department_id);
        if ($re) {
            return $department_id;
        }
        return false;
    }

    /**
     * @param $department_id
     * @param $name
     * @param $descr
     * @return bool
     * @throws \Exception
     */
    public static function updateDepartment($department_id, $name, $descr)
    {
        $db = getDB();
        $re = $db->getConnection()->update('departments', ['name'=>$name, 'descr'=>$descr], ['department_id'=>$department_id]);
        if ($re) {
            return true;
        }
        return false;
    }

    /**
     * @param $department_id
     * @return bool
     * @throws \Exception
     */
    public static function deleteDepartment($department_id)
    {
        $db = getDB();
        $re = $db->getConnection()->delete('departments', ['department_id'=>$department_id]);
        return true;
    }
} 