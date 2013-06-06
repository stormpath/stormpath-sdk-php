<?php

 /*
 * @desc Register an application with stormpath
 */
namespace Stormpath\Resource;

class Application
{
    private static $name;
    private static $description;
    private static $status;

    public static function getName()
    {
        return self::$name;
    }

    public static function setName($name)
    {
        self::$name = $value;
    }

    public static function getDescription()
    {
        return self::$description;
    }

    public static function setDescription($value)
    {
        self::$description = $value;
    }

    public static function getStatus()
    {
        return self::$status;
    }

    public static function setStatus($value)
    {
        self::$status = $value;
    }
}


?>
