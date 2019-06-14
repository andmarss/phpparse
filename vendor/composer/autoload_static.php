<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit37706f9a25538fa7265e681f827956d6
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'Sunra\\PhpSimple\\HtmlDomParser' => 
            array (
                0 => __DIR__ . '/..' . '/sunra/php-simple-html-dom-parser/Src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit37706f9a25538fa7265e681f827956d6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit37706f9a25538fa7265e681f827956d6::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit37706f9a25538fa7265e681f827956d6::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
