<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7e6d86e689f93434a2c1d20b01a4ade6
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Ascentech\\MassiveCsvImport\\' => 27,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Ascentech\\MassiveCsvImport\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit7e6d86e689f93434a2c1d20b01a4ade6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7e6d86e689f93434a2c1d20b01a4ade6::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit7e6d86e689f93434a2c1d20b01a4ade6::$classMap;

        }, null, ClassLoader::class);
    }
}
