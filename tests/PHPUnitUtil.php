<?php

namespace Meletisf\Settings\Tests;

class PHPUnitUtil
{
    /**
     * @throws \ReflectionException
     */
    public static function callMethod($obj, $name, array $args)
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $args);
    }
}
