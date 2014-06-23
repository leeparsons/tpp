<?php


class TppStoreControllerCart extends TppStoreAbstractBase {

    public static function getCartTotalsFormatted()
    {
        return geo::getInstance()->getCurrencyHtml() . self::getCartModel()->getTotalsFormatted();
    }

    public function applyRewriteRules()
    {
//        add_action( 'template_redirect', function() {
//            TppStoreControllerCart::getInstance()->templateRedirect();
//        } );

        add_rewrite_rule('shop/cart/([^/]+)?([^/+])?', 'index.php?tpp_pagename=tpp_cart&tpp_cart_method=$matches[1]', 'top');

        add_rewrite_rule('shop/cart/?', 'index.php?tpp_pagename=tpp_cart', 'top');

        add_rewrite_rule('shop/oneoffpayment/pay/?', 'index.php?tpp_pagename=tpp_checkout&tpp_checkout_method=oneoffpayment', 'top');

        add_rewrite_rule('shop/oneoffpayment/?', 'index.php?tpp_pagename=tpp_cart&tpp_cart_method=oneoffpayment', 'top');


        add_filter('query_vars', function($vars) {
            $vars[] = 'tpp_cart_method';
            return $vars;
        } );

        //flush_rewrite_rules(true);
    }


    public function templateRedirect()
    {

        $pagename = get_query_var('tpp_pagename');
        $cart_method = get_query_var('tpp_cart_method');

        if ($pagename == 'tpp_cart') {

            $this->_setWpQueryOk();



            switch ($cart_method) {
                case 'add':

                    //add ths item to the cart:

                    $this->add();


                    break;

                case 'oneoffpayment':

                    if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
                        if (($store = trim(filter_input(INPUT_GET, 'store', FILTER_SANITIZE_STRING))) != '') {
                            $this->redirectToLogin('redirect=/shop/oneoffpayment/?store=' . $store);
                        } else {
                            $this->redirectToLogin('redirect=/shop/oneoffpayment/');
                        }

                    }

                    $this->_setWpQueryOk();
                    $this->renderOneOffPayment();

                    break;

                case 'remove':

                    $this->removeItem();

                    break;

                case 'update':

                    $this->update();
                    break;



                default:

                    $cart = $this->getCartModel();

                    $cart->load(true);

                    //add_filter( 'wp_title', (function() use($cart) {TppStoreAbstractBase::pageTitle(array($cart));}), 10, 2);
                    $this->pageTitle($cart);
                    $this::$_meta_description = $cart->getSeoDescription();

                    wp_enqueue_style('cart', TPP_STORE_PLUGIN_URL . '/site/assets/css/cart.css');

                    include TPP_STORE_PLUGIN_DIR . 'site/views/cart/default.php';

                    //default view

                    break;
            }
            exit;
         } elseif ($pagename == 'tpp-generate-exchange-rates') {

            if (!class_exists('TppStoreAdapterPaypal')) {
                include TPP_STORE_PLUGIN_DIR . 'adapters/paypal/paypal.php';
            }

            $p = new TppStoreAdapterPaypal();

            if ( true === $p->generateExchangeRates() ) {
                $this->_setJsonHeader();
                $this->_exitStatus('success', false, array('message'    =>  'exchange rates updated'));
            } else {
                $this->_setJsonHeader();
                $this->_exitStatus('success', false, array('message'    =>  'Already up tp date'));

            }



        }


    }

    private function add()
    {

        //read from the post data to get the information being saved
        $product_id = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_NUMBER_INT);

        $product = $this->getProductModel()->setData(array('product_id'    =>  $product_id))->getProductById();

        if (intval($product->product_id) > 0 && intval($product->enabled) == 1) {

            //determine if this product can be added?
            $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);

            if (intval($quantity) < 0) {
                TppStoreMessages::getInstance()->addMessage('error', array('product'    =>  'Please enter a quantity greater than 0'));
                $this->redirect($_SERVER['HTTP_REFERER']);
            }

            if (intval($quantity) == 0) {
                $this->removeItem();
            }

            $option_id = filter_input(INPUT_POST, 'product_option', FILTER_SANITIZE_NUMBER_INT);

            if (!is_null($option_id) && !empty($option_id)) {
                $product->getProductOptionsModel()->setData(array('option_id'   =>  $option_id))->getOptionById();
            }
            $this->getCartModel()->add($product, $quantity);



        } else {
            $this->getCartModel()->removeItem($product_id);
            TppStoreMessages::getInstance()->addMessage('error', array('cart'   =>  'Could not add the product to your cart as the product no longer exists.'));
        }
        TppStoreMessages::getInstance()->saveToSession();





        $this->redirect('/shop/cart');
    }

    private function update()
    {
        //read from the post data to get the information being saved
        $product_id = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_NUMBER_INT);

        $product = $this->getProductModel()->setData(array('product_id'    =>  $product_id))->getProductById();

        if (intval($product->product_id) > 0 && intval($product->enabled) == 1) {

            //determine if this product can be added?
            $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);

            if (intval($quantity) == 0) {
                $this->removeItem();
            }

            $this->getCartModel()->update($product, $quantity);



        } else {
            //remove it from the cart if it's there!
            $this->getCartModel()->removeItem($product_id);
            TppStoreMessages::getInstance()->addMessage('error', array('cart'   =>  'Could not add the product to your cart as the product no longer exists.'));
        }
        TppStoreMessages::getInstance()->saveToSession();


        $this->redirect('/shop/cart');
    }


    private function removeItem()
    {
        //remove it from the cart regardless

        //read from the post data to get the information being saved
        $product_id = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_NUMBER_INT);
        $store_id = filter_input(INPUT_POST, 'store', FILTER_SANITIZE_NUMBER_INT);

        $this->getCartModel()->removeItem(intval($product_id), intval($store_id));

        TppStoreMessages::getInstance()->addMessage('message', array('cart' =>  'Product Removed'));
        TppStoreMessages::getInstance()->saveToSession();

        $this->redirect('/shop/cart');
    }

    private function renderOneOffPayment()
    {
        $this->pageTitle('One Off Payment');
        $this->setPageDescription('Make a one off payment');

        $stores = $this->getStoreModel()->getStores(1, 's.store_name ASC');

        $selected_store = filter_input(INPUT_GET, 'store', FILTER_SANITIZE_STRING);

        include TPP_STORE_PLUGIN_DIR . 'site/views/cart/oneoffpayment.php';

    }

}
