<?php
/**
 * User: leeparsons
 * Date: 16/01/2014
 * Time: 22:17
 */
 
class TppStoreAdminControllerStore extends TppStoreAbstractBase {

    public function renderApplicationList()
    {

        $applications = $this->getStoreApplicationModel()->getApplications();

        include TPP_STORE_PLUGIN_DIR . 'admin/views/store/applications.php';

    }

    public function renderApplication()
    {



        $application = $this->getStoreApplicationModel()->setData(array(
            'store_id'  =>  $_GET['sid']
        ))->getApplicationByStore();

        if (false === $application) {
            exit('No Application found');
        }

        $store = $this->getStoreModel()->setData(array(
            'store_id'  =>  $application->store_id
        ))->getStoreByID();

        $user = $this->getUserModel()->setData(array(
            'user_id'   =>  $store->user_id
        ))->getUserByID();

        include TPP_STORE_PLUGIN_DIR . 'admin/views/store/application.php';
    }


    public static function saveTppApplication()
    {



        $nonce = filter_input(INPUT_POST, 'save_tpp_application');

        if (!wp_verify_nonce($nonce, 'save_tpp_store_application')) {
            echo 'Not authorised!';
            echo '<a href="' . $_POST['_wp_http_referer'] . '">Go Back</a>';
            exit;
        }

        $sid = filter_input(INPUT_POST, 'store', FILTER_SANITIZE_NUMBER_INT);

        if (intval($sid) < 1) {
            echo 'Not authorised!';
            echo '<a href="' . $_POST['_wp_http_referer'] . '">Go Back</a>';
            exit;
        }

        $decline = filter_input(INPUT_POST, 'decline', FILTER_SANITIZE_NUMBER_INT);

        if (intval($decline) == 1) {

            $reason = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_STRING);

            if (trim($reason) == '') {
                echo 'You must provide a reason.<br>';
                echo '<a href="' . $_POST['_wp_http_referer'] . '">Go Back</a>';
                exit;
            }

            //save the store and notify its owner

            $store = TppStoreAdminControllerStore::getInstance()->getStoreModel()->setData(array(
                'store_id'  =>  $sid
            ))->getStoreById();
            $store->setData(array(
                    'approved'  =>  -1
                ))->save(true, true);

            $user = TppStoreAdminControllerStore::getInstance()->getUserModel()->setData(array(
                'user_id'   =>  $store->user_id
            ))->getUserById();

            $user->setData(array(
                    'user_type'    =>  'buyer'
                )
            );

            $user->save();


            ob_start();

            include TPP_STORE_PLUGIN_DIR . 'emails/application_rejection.php';

            $body = ob_get_contents();

            ob_end_clean();

            TppStoreAdminControllerStore::getInstance()->sendMail($user->email, 'Your store application was declined', $body);

        } else {
            $store = TppStoreAdminControllerStore::getInstance()->getStoreModel()->setData(array(
                'store_id'  =>  $sid,
            ))->getStoreById();
            $store->setData(array(
                    'approved'  =>  1
                ))->save(true, true);

            $user = TppStoreAdminControllerStore::getInstance()->getUserModel()->setData(array(
                'user_id'   =>  $store->user_id
            ))->getUserById();

            $user->setData(array(
                 'user_type'    =>  'store_owner'
                )
            );

            $user->save();

            ob_start();

            include TPP_STORE_PLUGIN_DIR . 'emails/application_successful.php';

            $body = ob_get_contents();

            ob_end_clean();

            TppStoreAdminControllerStore::getInstance()->sendMail($user->email, 'Your Photography Parlour store application is successful!', $body);

        }




        header('Location: ' . admin_url('admin.php?page=tpp-store-approvals'));
        exit;
    }
}