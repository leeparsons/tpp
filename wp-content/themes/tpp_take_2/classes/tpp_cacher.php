<?php

class TppCacher extends TppStoreAbstractInstantiable {

    protected $path = '';

    protected $cache_path = false;

    protected $cache_name = false;

    protected function __construct()
    {
        $this->path = WP_CONTENT_DIR . '/tpp_cache/';
    }

    public function saveCache($contents = '')
    {
        if (false === $this->cache_name) {
            return false;
        }
        if (true === $this->makeCacheDirectory()) {
            $f = fopen($this->path . $this->cache_path . $this->cache_name, 'w');
            $contents = serialize($contents);
            fwrite($f, $contents, strlen($contents));
            fclose($f);
            return true;
        } else {
            return false;
        }
    }

    public function readCache($ttl = 3600)
    {
        if (false === $this->cache_name) {
            return false;
        }
        if (true === $this->makeCacheDirectory()) {
            if (file_exists($this->path . $this->cache_path . $this->cache_name) && filemtime($this->path . $this->cache_path . $this->cache_name) > time() - $ttl) {
                $contents = file_get_contents($this->path . $this->cache_path . $this->cache_name);
                return unserialize($contents);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function deleteRecursive()
    {
        if (false === $this->cache_path) {
            return false;
        }

        $this->deleteFiles($this->path . $this->cache_path);
        $this->deleteDirectory($this->path . $this->cache_path);
    }

    private function deleteFiles($path = '')
    {

        if ($path == '') {
            return false;
        }

        if (substr($path, -1) != '/') {
            $path .= '/';
        }
        if (!file_exists($path)) {
            return false;
        }
        $files = scandir($path);

        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            if (is_dir($path . $file)) {
                $this->deleteFiles($path . $file);
                $this->deleteDirectory($path . $file);
            } else {
                @unlink($path . $file);
            }
        }

        $this->deleteDirectory($path);

    }

    private function deleteDirectory($dir = '')
    {

        if ($dir == '') {
            return false;
        }

        @rmdir($dir);
    }

    public function setCacheName($name = '')
    {
        if ($name == '') {
            $this->cache_name = false;
        } else {
            $this->cache_name = $name . '.chs';
        }
    }

    public function setCachePath($path = '')
    {
        if ($path == '') {
            $this->cache_path = false;
        } else {
            if (substr($path, -1) != '/') {
                $path .= '/';
            }
            $this->cache_path = $path;
        }
    }

    private function makeCacheDirectory()
    {

        if (false === $this->cache_path) {
            return false;
        }

        if (!file_exists($this->path . $this->cache_path)) {
            if (!@mkdir($this->path . $this->cache_path, 0777, true)) {
                return false;
            }

            if (!@chmod($this->path . $this->cache_path, 0777)) {
                return false;
            }
        }

        return true;
    }


}
