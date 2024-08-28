<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2d595ade954b279c56956c2314d39d94
{
    public static $prefixLengthsPsr4 = array (
        'i' => 
        array (
            'inIT\\ListingControllerExample\\' => 30,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'inIT\\ListingControllerExample\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'inIT\\ListingControllerExample\\Provider\\ProductProvider' => __DIR__ . '/../..' . '/src/Provider/ProductProvider.php',
        'inIT\\ListingControllerExample\\Provider\\SearchProvider' => __DIR__ . '/../..' . '/src/Provider/SearchProvider.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2d595ade954b279c56956c2314d39d94::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2d595ade954b279c56956c2314d39d94::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit2d595ade954b279c56956c2314d39d94::$classMap;

        }, null, ClassLoader::class);
    }
}
