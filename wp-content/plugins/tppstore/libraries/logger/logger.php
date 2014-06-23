<?php
/**
 * User: leeparsons
 * Date: 11/02/2014
 * Time: 12:56
 */


if (!class_exists('TppStoreControllerUser')) {
    include TPP_STORE_PLUGIN_DIR . 'site/controllers/user.php';
}

class TppStoreLibraryLogger extends TppStoreAbstractInstantiable {

    private $_directory = null;

    protected function __construct()
    {
        $this->dir = WP_CONTENT_DIR . '/store/logs/' . date('d-m-Y');
        $this->file = 'log.log';

        $this->_directory = new TppStoreDirectoryLibrary();

        $this->_directory->setDirectory($this->dir);

    }

    public function add($user_id = 0, $action = '', $message = '', $data = null, $type = 'message')
    {



        if (getenv('ENVIRONMENT') != 'local') {
        //    return false;
        }

        if (false === $this->_directory->createDirectory()) {
            return false;
        }

        if (intval($user_id) == 0) {
            if (false !== ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
                $user_id = $user->user_id;
            }
        }

        $file = $this->dir . '/' . $this->file;

        if (!file_exists($file)) {
            $f = fopen($file, 'a+');

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
            $f = fopen($file, 'a+');
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


        if ($type == 'error') {

            $html = array();
            $html[] = 'User: ' . $user->getName() . ' userid: ' . $user_id;
            $html[] = 'Action: ' . $action;
            $html[] = 'Message: ' . $message;
            $html[] = 'Browser: ' . TppStoreBrowserLibrary::getInstance()->getBrowserName() . ':' . TppStoreBrowserLibrary::getInstance()->getBrowserVersion();
            $html[] = 'data: ' . print_r($data, true);

            TppStoreControllerAccount::getInstance()->sendMail('parsolee@gmail.com', 'error on the photography parlour', '<html><body><p>' . implode('<br>', $html) . '</p></body></html>');
        }

    }

}
