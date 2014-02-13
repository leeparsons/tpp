<?php
/**
 * User: leeparsons
 * Date: 11/02/2014
 * Time: 12:56
 */


class TppStoreLibraryLogger extends TppStoreAbstractInstantiable {

    private $_directory = null;

    protected function __construct()
    {
        $this->dir = WP_CONTENT_DIR . '/store/logs/' . date('d-m-Y');
        $this->file = 'log.log';

        $this->_directory = new TppStoreDirectoryLibrary();

        $this->_directory->setDirectory($this->dir);

    }

    public function add($user_id = 0, $action = '', $message = '', $data = null)
    {

        if (false === $this->_directory->createDirectory()) {
            return false;
        }

        if ($user_id == null) {
            if (false !== ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
                $user_id = $user->user_id;
            }
        }

        $file = $this->dir . '/' . $this->file;

        if (!file_exists($file)) {
            $f = fopen($file, 'a');

            fputcsv($f, array(
                'Date',
                'user',
                'action',
                'message',
                'browser',
                'ip',
                'data'
            ));
        } else {
            $f = fopen($file, 'a');
        }



        fputcsv($f, array(
            date('Y-m-d H:i:s'),
            $user_id,
            $action,
            $message,
            TppStoreBrowserLibrary::getInstance()->getBrowserName() . ':' . TppStoreBrowserLibrary::getInstance()->getBrowserVersion(),
            $_SERVER['REMOTE_ADDR'],
            is_null($data)?'':serialize($data)
        ));

        fclose($f);

    }

}
