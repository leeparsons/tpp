<?php
/**
 * User: leeparsons
 * Date: 10/01/2014
 * Time: 08:11
 */
 
class TppStoreControllerOrders extends TppStoreAbstractBase {


    public function renderDashboardList(TppStoreModelUser $user, TppStoreModelStore $store)
    {

        $page = get_query_var('page');

        $page = $page?:1;

        $order_model = $this->getOrderModel()->setData(array(
            'user_id'   =>  $user->user_id
        ));

        $orders = $order_model->getOrdersByUser($page);
        $total_orders = $order_model->getOrdersByUser(null, true);


        unset($order_model);

        if (intval($store->store_id) > 1) {
            $product_count = TppStoreControllerDashboard::getInstance()->getProductCount($store);
            $mentor_sessions_count = TppStoreControllerDashboard::getInstance()->getMentorSessionCount($store);
        } else {
            $product_count = 0;
            $mentor_sessions_count = 0;
        }


        include TPP_STORE_PLUGIN_DIR . 'site/views/account/orders/history.php';
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