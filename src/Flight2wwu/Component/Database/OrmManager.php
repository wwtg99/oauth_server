<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/4/6
 * Time: 10:34
 */

namespace Flight2wwu\Component\Database;


use Flight2wwu\Common\ServiceProvider;

/**
 * Class OrmManager
 * @package Flight2wwu\Component\Database
 *
 * Load models in App/Model/Orm, models must extends OrmModel and not be static.
 * Table name use tableName property or lower class name if tableName is invalid.
 * Table key use tableKey property.
 */
class OrmManager implements ServiceProvider
{

    /**
     * Namespace for ORM models
     */
    const NS = 'App\Model\Orm\\';

    /**
     * @var array
     */
    private $models = [];

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

    }

    /**
     * OrmManager constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $name
     * @return null|OrmModel
     */
    public function getModel($name)
    {
        if (array_key_exists($name, $this->models)) {
            return $this->models[$name];
        } else {
            $rc = new \ReflectionClass(OrmManager::NS . $name);
            if ($rc->isSubclassOf('Flight2wwu\Component\Database\OrmModel')) {
                $ins = $rc->newInstance();
                $this->models[$name] = $ins;
                return $ins;
            }
        }
        return null;
    }

}