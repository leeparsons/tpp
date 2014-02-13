<?php
/**
 * User: leeparsons
 * Date: 07/01/2014
 * Time: 12:29
 */
 
class TppStoreControllerWishlist extends TppStoreAbstractBase {


    public function applyRewriteRules()
    {
        add_rewrite_rule('shop/wishlist/([^/]+)?', 'index.php?tpp_pagename=tpp_wishlist&method=$matches[1]', 'top');


    }

    public function templateRedirect()
    {

        $pagename = get_query_var('tpp_pagename');

        if ($pagename == 'tpp_wishlist') {
            $method = get_query_var('method');
            if (method_exists($this, $method)) {
                $this->$method();
            }
        }

    }

    public function add()
    {

        $wishlist = $this->getWishlistModel();

        if ($wishlist->readFromPost()) {
            $wishlist->save();
        }

        TppStoreMessages::getInstance()->saveToSession();

        $this->redirect($_SERVER['HTTP_REFERER']);
    }

}