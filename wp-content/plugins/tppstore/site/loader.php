<?php

if (!session_id()) {
    session_start();
}

//include the neccessary files
$paths = array(
    'factory/abstracts',
    'site/controllers',
    'site/models',
    'libraries'
);

    function getFiles($path)
    {
        if (file_exists(TPP_STORE_PLUGIN_DIR . $path)) {
            $files = scandir( TPP_STORE_PLUGIN_DIR . $path);

            foreach ( $files as $file ) {

                if (substr($file, 0, 1) == '.') {
                    continue;
                }

                if (is_dir(TPP_STORE_PLUGIN_DIR . $path . '/' . $file)) {
                    getFiles($path . '/' . $file);
                } else {
                    include TPP_STORE_PLUGIN_DIR . $path . '/' . $file;
                }
            }
            unset($files);
            unset($file);
        }
    }




foreach ($paths as $path) {
    getFiles($path);
}

unset($path);
unset($paths);

include TPP_STORE_PLUGIN_DIR . 'helpers/html.php';

require TPP_STORE_PLUGIN_DIR. 'site/site_hooks.php';