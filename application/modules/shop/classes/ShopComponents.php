<?php

/**
 * ShopComponents
 *
 * @version $id$
 * @author <dev@imagecms.net>
 * @property SSettings $SSettings
 */
class ShopComponents
{

    /**
     *
     * @var array
     */
    protected static $_components = [];

    /**
     * Load and return class
     *
     * @param string $className
     * @access public
     * @return object
     */
    public function __get($className) {

        if (!isset(self::$_components[$className])) {
            $class = new $className();
            self::$_components[$className] = &$class;
            return $class;
        } else {
            return self::$_components[$className];
        }
    }

}