<?php
/**
 * User: leeparsons
 * Date: 03/01/2014
 * Time: 12:32
 */
 
class TppStoreControllerDiscount extends TppStoreAbstractBase {


    public function applyRewriteRules()
    {
//        add_action( 'template_redirect', function() {
//            TppStoreControllerDiscount::getInstance()->templateRedirect();
//        } );


        add_rewrite_rule('shop/discounts/create', 'index.php?tpp_pagename=tpp_discount_create', 'top');



        //flush_rewrite_rules(true);
    }


    public function templateRedirect()
    {

        $pagename = get_query_var('tpp_pagename');

        switch ($pagename) {
            case 'tpp_discount_create':

                $this->_createDiscount();

                break;

            default:

                break;
        }

    }


    /*
     * creates a discount for this product and user!
     */
    private function _createDiscount()
    {
        //make a discount

        $this->_setJsonHeader();

        $discount = $this->getUserDiscountModel();

        if ($discount->readFromPost()) {

            $discount->setData(array(
                'max_uses'  =>  0
            ));

            if ($discount->save()) {
                $this->_exitStatus('success', false, json_encode(array('total'  =>  $this->getCartModel()->getTotalsFormatted())));
            } else {

                if ($discount->message !== '') {
                    $this->_exitStatus($discount->message, true);
                } else {
                    $this->_exitStatus('could not create discount', true);
                }
            }

        } else {
            $this->_setWpQuery403();
            $this->_exitStatus('forbidden', true);
        }



    }

}