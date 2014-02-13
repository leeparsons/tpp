<?php
/**
 * User: leeparsons
 * Date: 06/01/2014
 * Time: 12:46
 */
 
class TppStoreControllerReview extends TppStoreAbstractBase {


    public function applyRewriteRules()
    {

        add_rewrite_rule('shop/review/([^/]+)?', 'index.php?tpp_pagename=tpp_review&method=$matches[1]', 'top');

    }

    public function templateRedirect()
    {

        $pagename = get_query_var('tpp_pagename');

        if ($pagename == 'tpp_review') {

            $method = get_query_var('method');

            if ($method !== '' && method_exists($this, $method)) {
                $this->$method();
            }
        }

    }


    private function add()
    {

        $redirect = $_SERVER['HTTP_REFERER'];


        if ($redirect == '' || stripos($redirect, 'shop/review/add') !== false) {
            $redirect = '/';
        }

        //determine if the user is logged in?
        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            $this->_setWpQuery403();
            $this->redirectToLogin($redirect);
        }

        $robot = filter_input(INPUT_POST, 'mc_first_name', FILTER_SANITIZE_STRING);

        if ($robot != '') {
            TppStoreMessages::getInstance()->addMessage('error', 'We have detected you are a bot and un authorised to access this functionality.');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirect($redirect);
        }

        $this->_setWpQueryOk();

        $review = $this->getRatingModel();

        if (false !== $review->readFromPost()) {
            $review->save();
            TppStoreMessages::getInstance()->saveToSession();
        }

        $this->redirect($redirect . '#review_form');


        //determine where the review came from, and show this page if it exists?

    }

}