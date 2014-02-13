<?php
/**
 * User: leeparsons
 * Date: 29/01/2014
 * Time: 20:47
 */

if (!class_exists('TppStoreLibraryAbstractFile')) {
    include TPP_STORE_PLUGIN_DIR . 'libraries/file/file.php';
}

class TppStoreLibraryFile extends TppStoreLibraryAbstractFile {

    public function validateUploadedFile()
    {
        return parent::validateUploadedFile();
    }



}