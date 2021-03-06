<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb2404585cc286219d99ebd22b96583ec
{
    public static $prefixLengthsPsr4 = array (
        'E' => 
        array (
            'Equip\\Service\\' => 14,
            'Equip\\Module\\' => 13,
            'Equip\\Misc\\' => 11,
            'Equip\\Layout\\' => 13,
            'Equip\\Field\\' => 12,
            'Equip\\Engine\\' => 13,
            'Equip\\' => 6,
        ),
        'D' => 
        array (
            'DeepCopy\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Equip\\Service\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/services',
        ),
        'Equip\\Module\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/modules',
        ),
        'Equip\\Misc\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/misc',
        ),
        'Equip\\Layout\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/layouts',
        ),
        'Equip\\Field\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/fields',
        ),
        'Equip\\Engine\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/engines',
        ),
        'Equip\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
        'DeepCopy\\' => 
        array (
            0 => __DIR__ . '/..' . '/myclabs/deep-copy/src/DeepCopy',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb2404585cc286219d99ebd22b96583ec::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb2404585cc286219d99ebd22b96583ec::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
