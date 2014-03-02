<?php
/**
 * User: leeparsons
 * Date: 10/01/2014
 * Time: 08:11
 */
 
class TppStoreControllerOrders extends TppStoreAbstractBase {

    public function renderPurchase($order_id = 0, $user = false, $store = false)
    {



        if (false === $user && false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            $this->redirectToLogin();
        }

        if (intval($order_id) < 1) {
            if ($user->user_type == 'buyer') {
                $this->redirectToAccount();
            } else {
                $this->redirectToDashboard();
            }
        }



        if (false === $store && false === ($store = TppStoreControllerStore::getInstance()->loadStoreFromSession())) {
            $product_count = 0;
            $mentor_count = 0;
            $event_count = 0;

        } else {
            $product_count = TppStoreControllerDashboard::getInstance()->getProductCount($store);
            $mentor_count = TppStoreControllerDashboard::getInstance()->getMentorCount($store);
            $mentor_count = TppStoreControllerDashboard::getInstance()->getEventCount($store);

        }



        $this->enqueueAccountResources();


        $order = $this->getOrderModel()->setData(array(
            'order_id'  =>  $order_id
        ));
        $order->getOrderById();





        if ($order->user_id != $user->user_id) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to view this order');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToAccount();
        } else {

            $store = TppStoreControllerStore::getInstance()->loadStoreFromSession();


            $order_items = $this->getOrderItemsModel()->setData(array(
                'order_id'  =>  $order_id
            ))->getLineItems(true);

            $payments = $this->getPaymentModel()->setData(array(
                'order_id'  =>  $order->order_id
            ))->getPaymentsByOrder();
            include TPP_STORE_PLUGIN_DIR . 'site/views/account/purchases/purchase.php';

            exit;
        }

    }

    public function renderReceivedOrder(TppStoreModelUser $user, TppStoreModelStore $store)
    {

        if (false === $user && false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            $this->redirectToLogin();
        }

        $order_ref = get_query_var('args');

        if ($order_ref == '') {
            if ($user->user_type == 'buyer') {
                $this->redirectToAccount();
            } else {
                $this->redirectToDashboard();
            }
        }


        if (false === $store && false === ($store = TppStoreControllerStore::getInstance()->loadStoreFromSession())) {
            $product_count = 0;
            $mentor_count = 0;
        } else {
            $product_count = TppStoreControllerDashboard::getInstance()->getProductCount($store);
            $mentor_count = TppStoreControllerDashboard::getInstance()->getMentorCount($store);
        }


        $this->enqueueAccountResources();

        $order = $this->getOrderModel()->setData(array(
            'ref'  =>  $order_ref
        ));
        $order->getOrderByRef();

        if ($order->store_id != $store->store_id) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to view this order');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToAccount();
        } else {

            $order_items = $this->getOrderItemsModel()->setData(array(
                'order_id'  =>  $order->order_id
            ))->getLineItems(true);

            $payments = $this->getPaymentModel()->setData(array(
                'order_id'  =>  $order->order_id
            ))->getPaymentsByOrder();
            include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/orders/order.php';

            exit;
        }

    }

    public function renderReceivedOrders(TppStoreModelUser $user, TppStoreModelStore $store)
    {
        $page = get_query_var('page');

        $page = $page?:1;

        $order_model = $this->getOrderModel()->setData(array(
            'store_id'   =>  $store->store_id
        ));

        $orders = $order_model->getOrdersReceivedByStore($page);
        $total_orders = $order_model->getOrdersReceivedByStore(null, true);

        include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/orders/history.php';

    }

    public function renderPurchaseList(TppStoreModelUser $user, TppStoreModelStore $store)
    {

        $page = get_query_var('page');

        $page = $page?:1;

        $order_model = $this->getOrderModel()->setData(array(
            'user_id'   =>  $user->user_id
        ));

        $orders = $order_model->getOrdersByUser($page);
        $total_orders = $order_model->getOrdersByUser(null, true);


        unset($order_model);



        include TPP_STORE_PLUGIN_DIR . 'site/views/account/purchases/history.php';
        exit;
//
//
//        if (false !== ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
//
//            if (false !== ($store = TppStoreControllerStore::getInstance()->loadStoreFromSession())) {
//                if (intval($store->store_id) > 0) {
//                    //get the product count and mentor count
//                    //for the sidebar
//                    $products_model = $this->getProductsModel();
//                    $products_model->setData(array('store_id'    =>  $store->store_id));
//                    $product_count = $products_model->getProductCountByStore();
//                    $mentor_sessions_count = $products_model->getProductCountByStore(true);
//
//                }
//            }
//
//        }
//
//
//        $orders = $this->getOrderModel()->setData(array(
//            'user_id'   =>  $user->user_id
//        ))->getOrdersByUser();
//
//        include TPP_STORE_PLUGIN_DIR . 'site/views/account/orders/history.php';
//
//        exit;
    }

}