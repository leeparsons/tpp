<?php 


 
class TppStoreControllerLanding extends TppStoreAbstractBase {


    public function applyRewriteRules()
    {
        add_rewrite_rule('shop/landing/success/checkout/([^/]+)?', 'index.php?tpp_pagename=tpp_checkout_landing_success&tpp_checkout_method=$matches[1]', 'top');
        add_rewrite_rule('shop/landingpage/send_email', 'index.php?tpp_pagename=tpp_send_email', 'top');
    }

    public function templateRedirect()
    {

        $pagename = get_query_var('tpp_pagename');
        if ($pagename == 'tpp_send_email') {
        	$this->_sendLinkEmail();
        } elseif ($pagename == 'tpp_checkout_landing_success') {
        	$method = get_query_var('tpp_checkout_method');
            $this->showLandingPageSuccess($method);
            exit;
        }
	}

	private function showLandingPageSuccess($tpp_checkout_method = '')
    {
		$order_id = isset($_SESSION['guest_order_id']) ? $_SESSION['guest_order_id'] : 0;

    	if (intval($order_id) > 0) {
    		$_SESSION['guest_order_id'] = null;
    		unset($_SESSION['guest_order_id']);
    		$this->redirect('/shop/landing/success/checkout/' . $tpp_checkout_method . '?goid=' . $order_id);
    	}

        $order_id = isset($_GET['goid']) ? $_GET['goid'] : 0;

        $order = $this->getOrderModel();

        $order->setData(array('order_id'    =>  $order_id))->getOrderById();


        if ($order->order_id != null) {

            $order_items =    $this->getOrderItemsModel()->setData(array(
                'order_id'      =>  $order->order_id
            ))->getLineItems(true);

            switch ($tpp_checkout_method) {
                case 'download':
                include(TPP_STORE_PLUGIN_DIR . 'site/views/checkout/landing/success/download.php');
                break;
            }

        } else {
            $title = 'Order not found or expired';
            $message = 'Sorry, we could not find your order or it has expired.<br><br><a href="/shop/category/marketing/" class="btn btn-primary">Continue Shopping</a>';
            include(TPP_STORE_PLUGIN_DIR . 'site/views/404.php');
        }

    }


	private function _sendLinkEmail()
	{
		$email = trim(filter_input(INPUT_POST, 'yemail', FILTER_SANITIZE_STRING));

        	$email = filter_var($email, FILTER_VALIDATE_EMAIL);

        	if (false === $email) {
        		TppStoreMessages::getInstance()->addMessage('error', 'Enter a valid email address');
          	} else {

          		//save in the database and send it!
          		
          		$order = $this->getOrderModel();


          		$order->setData(array('order_id'	=>	filter_input(INPUT_POST, 'order', FILTER_SANITIZE_NUMBER_INT)))->getOrderById();


          		if ($order->order_type == 'guest_checkout') {



          			if ($order->order_date >= strtotime('-7day')) {

          				$order_items = $this->getOrderItemsModel()->setData(array('order_id'	=>	$order->order_id))->getLineItems(true);

          				foreach ($order_items as $item) {
			          		$message = '<p>You can download your ebook here: ' . get_site_url() . $item->getDownloadUrl(true, false, true) . '</p>';

          				}


						$this->sendMail($email, 'Your download Link', $message);

        				TppStoreMessages::getInstance()->addMessage('message', 'Email sent');


          			} else {
          				TppStoreMessages::getInstance()->addMessage('error', 'Sorry, the download link has expired');
          			}
          		} else {
          			TppStoreMessages::getInstance()->addMessage('error', 'Sorry, we could not verify your details - please contact us');
          		}


          		
          	}

           	TppStoreMessages::getInstance()->saveToSession();

          	$this->redirect($_SERVER['HTTP_REFERER']);
	}
}