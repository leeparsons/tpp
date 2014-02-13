<?php
/**
 * User: leeparsons
 * Date: 12/01/2014
 * Time: 17:46
 */
 
class TppStoreControllerMentors extends TppStoreAbstractBase {


    public function applyRewriteRules()
    {

        add_rewrite_rule('shop/category/mentors/sort/([^/]+)?', 'index.php?tpp_pagename=tpp_mentors&args=$matches[1]', 'top');

        add_rewrite_rule('shop/category/mentors/?', 'index.php?tpp_pagename=tpp_mentors', 'top');

    }

    public function templateRedirect()
    {

        $pagename = get_query_var('tpp_pagename');

        switch ($pagename) {
            case 'tpp_mentors':

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
                $products = $this->getMentorsModel()->getMentorSessionList($page, 'm.mentor_name', 'ASC');
                break;

            case 'z-a':
                $products = $this->getMentorsModel()->getMentorSessionList($page, 'm.mentor_name');
                break;

            case 'highest-price':
                $products = $this->getMentorsModel()->getMentorSessionList($page, 'p.price', 'ASC');
                break;
            case 'lowest-price':
                $products = $this->getMentorsModel()->getMentorSessionList($page, 'p.price');
                break;

            case 'lowest-rated':
                $products = $this->getMentorsModel()->getMentorSessionList($page, 'rating', 'ASC');

                break;
            default:
                $products = $this->getMentorsModel()->getMentorSessionList($page);
                break;
        }

        if (count($products) > 0) {
            $mentor_ids = array();

            foreach ($products as $mentor) {
                $mentor_ids[] = $mentor->mentor_id;
            }

            unset($mentor);

            $specialisms = $this->getMentorSpecialismsModel()->getSpecialismsByMentors($mentor_ids);
            unset($mentor_ids);



            foreach ($products as $mentor) {
                if (isset($specialisms[$mentor->getMentor()->mentor_id])) {
                    $mentor->getMentor()->getSpecialism(false)->setSpecialisms($specialisms[$mentor->mentor_id]);
                }
            }

            unset($specialisms);
            unset($mentor);

        }

        //get the specialisms for the select mentors!

        include TPP_STORE_PLUGIN_DIR . 'site/views/mentors.php';

        exit;
    }

}