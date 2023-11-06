<?php
namespace Compress\Platform;

class Xdg
{
    /**
     * Whether the system uses Freedesktop
     * @var bool
     */
    private static $isUsed;

    /**
     * Returns whether the system uses Freedesktop
     * @return bool [description]
     */
    public static function isUsed()
    {
        if (null === self::$isUsed) {
            self::$isUsed = self::hasXdgVar();
        }

        return self::$isUsed;
    }

    /**
     * Returns whether $_SERVER contains at least one XDG_ variable
     * @return bool
     */
    private static function hasXdgVar()
    {
        foreach (array_keys($_SERVER) as $key) {
            if (substr($key, 0, 4) === 'XDG_') {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns data home according to XGD spec
     * @see https://specifications.freedesktop.org/basedir-spec/basedir-spec-latest.html
     * @param  string $home User's home directory
     * @return string
     */
    public static function getDataHome($home)
    {
        return getenv('XDG_DATA_HOME') ?: $home.'/.local/share';
    }

    /**
     * Returns config home according to XDG spec
     * @see https://specifications.freedesktop.org/basedir-spec/basedir-spec-latest.html
     * @param  string $home User's home directory
     * @return string
     */
    public static function getConfigHome($home)
    {
        return getenv('XDG_CONFIG_HOME') ?: $home.'/.config';
    }

    /**
     * Returns cache home according to XDG spec
     * @see https://specifications.freedesktop.org/basedir-spec/basedir-spec-latest.html
     * @param  string $home User's home directory
     * @return string
     */
    public static function getCacheHome($home)
    {
        return getenv('XDG_CACHE_HOME') ?: $home.'/.cache';
    }
}