<?php
namespace Compress\Platform;

class Platform {
    private static $home;
     
    public static function isWindows()
    {
        return defined('PHP_WINDOWS_VERSION_BUILD');
    }
    
    public static function getUserHome()
    {
        if (null === self::$home) {
            self::$home = self::findUserHome();
        }

        return self::$home;
    }

    private static function findUserHome()
    {
        if (false !== ($home = getenv('HOME'))) {
            return $home;
        }

        if (self::isWindows() && false !== ($home = getenv('USERPROFILE'))) {
            return $home;
        }

        if (function_exists('posix_getuid') && function_exists('posix_getpwuid')) {
            $info = posix_getpwuid(posix_getuid());

            return $info['dir'];
        }

        throw new \RuntimeException('Could not determine user directory');
    }
    
      
    public static function getDataDir()
    {
        return self::getDir('getDataHome');
    }
    
    public static function getConfigDir()
    {
        return self::getDir('getConfigHome');
    }
    
    public static function getCacheDir()
    {
        return self::getDir('getCacheHome');
    }
    
      
    private static function getDir($xdgFunc)
    {
        $home = self::getUserHome();

        if (self::isWindows()) {
            return self::findWindowsDataDir() ?: $home;
        }

        if (Xdg::isUsed()) {
            return call_user_func([Xdg::class, $xdgFunc], $home);
        }

        return $home;
    }
    
    private static function findWindowsDataDir()
    {
        return getenv('LOCALAPPDATA') ?: getenv('APPDATA');
    }
}