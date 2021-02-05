<?php 

class FileCacher
{
    public static function put($key, $value, $expire_after = 0)
    {
        $file_path = self::_createFilePathWithKey($key);

        $value = [
            "created_at" => time(),
            "data" => is_array($value) ? $value = json_encode($value) : $value
        ];
        if ($expire_after != 0) {
            $value['expire_at'] = time() + $expire_after;
        }

        return self::_putCacheFileContent($file_path, json_encode($value));
    }

    public static function get($key, $default = null)
    {
        $file_path = self::_createFilePathWithKey($key);

        if (self::_cacheFileExists($file_path)) {
            $value = json_decode(self::_readCacheContent($file_path), true);
            if (!empty($value['expire_at']) && $value['expire_at'] <= time()) {
                $value = self::_getDefault($default);
                self::forget($key);
            } else {
                $value = json_decode($value['data'], true) ?: $value['data'];
            }
        } else {
            $value = self::_getDefault($default);
        }

        return $value;
    }

    public static function remember($key, $default = null, $expire_after = 0)
    {
        if (!self::has($key)) {
            $value = self::_getDefault($default);
            self::put($key, $value, $expire_after);
        }

        return self::get($key, $default);
    }

    public static function pull($key, $default = null)
    {
        $file_path = self::_createFilePathWithKey($key);

        if (self::_cacheFileExists($file_path)) {
            $value = self::get($key);
            self::forget($key);
        } else {
            $value = self::_getDefault($default);
        }

        return $value;
    }

    public static function has($key)
    {
        return self::get($key, "default") != "default";
    }

    public static function forget($key)
    {
        $file_path = self::_createFilePathWithKey($key);

        self::_removeFileCache($file_path);

        return !self::_cacheFileExists($file_path);
    }

    public static function clear()
    {
        $cache_dir = self::_prepareDirPath(self::$_cache_dir ?: self::_defaultCacheDir());
        $caches = scandir($cache_dir);

        foreach ($caches as $cache) {
            if (!self::_strStartsWith($cache, ".")) {
                unlink($cache_dir . $cache);
            }
        }
        die();
    }

    private static function _putCacheFileContent($file_path, $content)
    {
        return file_put_contents($file_path, $content) !== false;
    }

    private static function _readCacheContent($file_path)
    {
        return file_get_contents($file_path);
    }

    private static function _cacheFileExists($file_path)
    {
        return file_exists($file_path);
    }

    private static function _removeFileCache($file_path)
    {
        @unlink($file_path);
    }

    private static function _createFilePathWithKey($key)
    {
        $cache_dir = self::$_cache_dir ?: self::_defaultCacheDir();
        self::_makeCacheDir(self::$_cache_dir ?: null);
        return self::_prepareDirPath($cache_dir) . md5($key);
    }

    private static function _prepareDirPath($file_path)
    {
        $slash = self::_strStartsWith($file_path, "/") ? "" : "/";
        $endSlash = self::_strEndsWith($file_path, "/") ? "" : "/";
        return "." . $slash . $file_path . $endSlash;
    }

    private static function _strStartsWith($str, $value)
    {
        return substr($str, 0, strlen($value)) == $value;
    }

    private static function _strEndsWith($str, $value)
    {
        return substr($str, strlen($str) - strlen($value)) == $value;
    }

    private static function _defaultCacheDir()
    {
        return "/storage/cache/";
    }

    private static function _makeCacheDir($dir = null)
    {
        if ($dir) {
            $dir = self::_strStartsWith($dir, "/") ? substr($dir, 1) : $dir;
            $parts = explode("/", $dir);
            if (empty($parts)) {
                $parts = explode("\\", $dir);
            }
            $path = "";
            foreach ($parts as $part) {
                if (empty(trim($part))) {
                    return;
                }
                $path .= "/" . $part;
                $path = self::_strStartsWith($path, "/") ? substr($path, 1) : $path;
                @mkdir($path);
            }
        } else {
            @mkdir("storage");
            @mkdir("storage/cache");
        }
    }

    private static function _getDefault($default)
    {
        return is_callable($default) ? $default() : $default;
    }

    private static $_cache_dir;

    public static function setCacheDir($dir)
    {
        self::$_cache_dir = $dir;
    }
}
