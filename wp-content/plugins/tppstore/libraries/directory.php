<?php
/**
 * User: leeparsons
 * Date: 10/12/2013
 * Time: 08:13
 */
 
class TppStoreDirectoryLibrary {

    private $directory = '';

    public function setDirectory($directory = '')
    {
        if (strpos($directory, WP_CONTENT_DIR) === false) {
            $this->directory = '';
            return;
        }

        if (substr($directory, -1) !== '/') {
            $directory .= '/';
        }

        $this->directory = $directory;
    }

    public function directoryExists()
    {
        if (false === strpos($this->directory, WP_CONTENT_DIR)) {
            return false;
        }

        return file_exists($this->directory) && is_dir($this->directory);
    }

    public function getFiles($full_path = true)
    {
        if ($this->directory !== '') {

            $files = scandir($this->directory);
            $return = array();

            foreach ($files as $tmp) {
                if (is_dir($this->directory . $tmp)) {
                    continue;
                }

                $return[] = $full_path === true?$this->directory . $tmp:$tmp;
            }

            return $return;

        } else {
            return array();
        }
    }

    public function deleteDirectory()
    {

        $this->deleteFiles($this->directory);
        @rmdir($this->directory);

        //determine if there are any files in this directory


    }

    private function deleteFiles($path = '')
    {
        if ($path == '') {
            return false;
        }

        if (substr($path, -1) != '/') {
            $path .= '/';
        }

        if (is_dir($path)) {

            //get the files in this path and cycle through them to delete them!
            $files = scandir($path);

            foreach ($files as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (is_file($path . $file)) {
                    @unlink($path . $file);
                } else {
                    $this->deleteFiles($path . $file);
                }
            }


        }

        @rmdir($path);


    }

    public function createDirectory($dir = '')
    {

        if ($dir == '') {
            $dir = $this->directory;
        }

        if ($dir == '' || strpos($dir, '..') !== false || strpos($dir, '/.') !== false || strpos($dir, './') !== false) {
            return false;
        }


        if (!file_exists($dir)) {
            if (!@mkdir($dir, 0777, true)) {
                return false;
            }



                //try and break it down
                $path = substr($dir, strlen(WP_CONTENT_DIR . '/uploads/'));

                $path_array = explode('/', $path);

                $tmp = WP_CONTENT_DIR . '/uploads';

                foreach ($path_array as $path) {
                    if ($path == '') {
                        continue;
                    }

                    $tmp .= '/' . $path;

                    if (!@chmod($tmp, 0777)) {
                        return false;
                    }

                }

            }


        return true;
    }


}