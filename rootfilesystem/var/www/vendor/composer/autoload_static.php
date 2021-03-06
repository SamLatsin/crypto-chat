<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit580ec79631495856b6f75c0c597e5176
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Swoole\\' => 7,
        ),
        'A' => 
        array (
            'APP\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Swoole\\' => 
        array (
            0 => __DIR__ . '/..' . '/eaglewu/swoole-ide-helper/src',
        ),
        'APP\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit580ec79631495856b6f75c0c597e5176::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit580ec79631495856b6f75c0c597e5176::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit580ec79631495856b6f75c0c597e5176::$classMap;

        }, null, ClassLoader::class);
    }
}
