<?php
/**
 * User: leeparsons
 * Date: 17/02/2014
 * Time: 21:52
 */
 
class TppStoreControllerEvents extends TppStoreAbstractBase {


    public function applyRewriteRules()
    {
        add_rewrite_rule('shop/category/workshops-events/sort/([^/]+)?', 'index.php?tpp_pagename=tpp_events&args=$matches[1]', 'top');

        add_rewrite_rule('shop/category/workshops-events/?', 'index.php?tpp_pagename=tpp_events', 'top');

    }

    public function templateRedirect()
    {

        $pagename = get_query_var('tpp_pagename');

        switch ($pagename) {
            case 'tpp_events':

                $this->_setWpQueryOk();
                $this->renderList();
                break;

            default:
                //do nothing
                break;
        }


    }

    private function renderList()
    {
        $page = get_query_var('paged')?:1;

        $args = get_query_var('args');


        switch ($args) {

            case 'a-z':
                $events = $this->getEventsModel()->getEvents($page, 'p.product_title', 'ASC');
                break;

            case 'z-a':
                $events = $this->getEventsModel()->getEvents($page, 'p.product_title', 'DESC');
                break;

            case 'highest-price':
                $events = $this->getEventsModel()->getEvents($page, 'p.price', 'ASC');
                break;
            case 'lowest-price':
                $events = $this->getEventsModel()->getEvents($page, 'p.price');
                break;

            case 'lowest-rated':
                $events = $this->getEventsModel()->getEvents($page, 'rating', 'ASC');

                break;
            default:
                $events = $this->getEventsModel()->getEvents($page);
                break;
        }

        include TPP_STORE_PLUGIN_DIR . 'site/views/events/list.php';
        exit;
    }

    public function renderDashboardList(TppStoreModelStore $store, TppStoreModelUser $user, $product_count = 0, $event_count = 0, $mentor_count = 0)
    {
        $products = $this->getProductsModel()->setData(array(
            'store_id'  =>  $store->store_id
        ))->getProductsByStore(0, 20, 'all', false, true);

        include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/events/list.php';

    }

    public function renderEventDashboardForm(TppStoreModelStore $store, TppStoreModelUser $user)
    {

        if (!class_exists('TppStoreBrowserLibrary')) {
            include TPP_STORE_PLUGIN_DIR . 'libraries/browser.php';
        }


        $product_id = intval(get_query_var('product_id'));

        if (is_null($product_id) || $product_id == '') {

            //new product
            $product = $this->getEventModel()->setData(array(
                'store_id'    =>  $store->store_id
            ));
        } else {
            $product = $this->getEventModel()->setData(array(
                'product_id'    =>  $product_id
            ))->getProductById('all', 5);
        }



        if ($product->store_id != $store->store_id) {
            TppStoreMessages::getInstance()->addMessage('error', array('product'    =>  'You are not authorised to edit this product'));
            TppStoreMessages::getInstance()->saveToSession();
            include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/default.php';
        } else {




            $already_enabled = $product->enabled;

            if ($product->readFromPost()) {
                $preview = filter_input(INPUT_POST, 'preview', FILTER_SANITIZE_NUMBER_INT);

                //trial
                if (intval($preview) === 1) {
                    //show the preview!

                    $images = $this->getProductImagesModel()->setData(array(
                        'store_id'  =>  $store->store_id
                    ));
                    $images->retrieveUsingSession(true);

                    //arrange the images!

                    $tmp = $images->images;

                    $ordered_images = array();

                    foreach ($tmp as $index => $image) {
                        $ordered_images[intval($image->ordering)] = $image;
                    }

                    ksort($ordered_images);



                    $product->getDiscount(false)->readFromPost();


                    //store any images into this product...
                    TppStoreControllerDashboard::getInstance()->setPreviewSession($product, $ordered_images);

                    $this->_setJsonHeader();
                    $this->_exitStatus('success', false, array(
                        'location'  =>  '/shop/product/preview'
                    ));

                }



                if ($product->save()) {



//                            if ($preview == 1) {
//
//                                $this->deleteTempStorePathSession();
//                                $this->saveTempStorePathSession($product->product_id, $product->store_id);
//
//                                $this->_setJsonHeader();
//                                $this->_exitStatus('success', false, array(
//                                    'location'  =>  $product->getPermalink() . '?preview=1',
//                                    'product'   =>  $product->product_id
//                                ));
//                            } else {
                    TppStoreControllerDashboard::getInstance()->deleteTempStorePathSession();
                        if (intval($product->enabled) == 1 && intval($store->enabled) == 1 && $product->getImages(0, false, true) > 0) {
                            TppStoreMessages::getInstance()->addMessage('message', 'Congratulations you have listed your event and it is now live on the site! <a href="' . $product->getPermalink() . '" class="btn btn-primary" target="_blank">View now</a>');
                            TppStoreMessages::getInstance()->addMessage('message', '<div class="align-left" style="margin-right:10px;">Share your event: </div><div class="align-left" style="margin-right:10px;"><div class="fb-share-button" data-href="' . $product->getPermalink() . '" data-type="button"></div></div> <div class="align-left" style="margin-right:10px;"><script type="IN/Share" data-url="' . $product->getPermalink() . '" data-counter="right"></script><script src="//platform.linkedin.com/in.js" type="text/javascript">lang: en_US</script></div> <div class="align-left" style="margin-right:10px;"><a href="https://twitter.com/share" class="twitter-share-button" data-url="' . $product->getpermalink() . '">Tweet</a></div><div class="align-left" style="margin-right:10px;"><div class="g-plusone" data-href="' . $product->getPermalink() . '"></div></div>');                        }
                        else {
                            TppStoreMessages::getInstance()->addMessage('message', 'Your event has been saved');
                        }

                        TppStoreMessages::getInstance()->saveToSession();
                        $this->redirectToDashboard('event/edit/' . $product->product_id);

                    //}
                } else {
                    if (intval($product->product_id) == 0) {
                        $product->setData(array(
                            'enabled'   =>  0
                        ));
                    }
                }
//                        } elseif ($preview == 1) {
//                            $this->_exitStatus('error', true, array('errors' =>  TppStoreMessages::getInstance()->getMessages()));
//                        }
            } else {



                TppStoreControllerDashboard::getInstance()->saveTempStorePathSession($product_id, $store->store_id);
            }

            $store_id = $store->store_id;

        }

        wp_enqueue_script('jquery-ui-datepicker');

        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');


        $categories_model = $this->getCategoriesModel();
        $categories_model->getCategories(array(
            'heirarchical'  =>  true,
            'type'          =>  'assoc',
            'category_id'   =>  array(
                3
            )
        ));

        $categories = $categories_model->categories;


        include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/event/form.php';

    }

}