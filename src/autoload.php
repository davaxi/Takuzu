<?php
/**
 * @param $className
 */
function davaxi_takuzu_autoload($className)
{
    $classPath = explode('\\', $className);
    if ($classPath[0] === 'Davaxi' && $classPath[1] === 'Takuzu') {
        $classPath = array_slice($classPath, 2);
        $filePath = dirname(__FILE__) . '/' . implode('/', $classPath) . '.php';
        if (file_exists($filePath)) {
            require_once($filePath);
        }
    }
}
spl_autoload_register('davaxi_takuzu_autoload');