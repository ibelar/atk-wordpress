<?php

require_once 'ClassLoader.php';
//require_once __DIR__ .'../../../composer/ClassLoader.php';

class PluginAutoLoader
{
    public static function getLoader()
    {
        return new ClassLoader();
    }
}