<?php
/**
 * User: leeparsons
 * Date: 12/01/2014
 * Time: 17:46
 */
 
class TppStoreControllerMentors extends TppStoreAbstractBase {


    public function applyRewriteRules()
    {
        add_rewrite_rule('shop/mentor/([^/]+)?', 'index.php?tpp_pagename=tpp_mentor&args=$matches[1]', 'top');

        add_rewrite_rule('shop/mentor/([^/]+)/page/([^/]+)/?', 'index.php?tpp_pagename=tpp_mentor&args=$matches[1]&page=$matches[2]', 'top');


        add_rewrite_rule('shop/category/mentors/sort/([^/]+)?', 'index.php?tpp_pagename=tpp_mentors&category_slug=mentors&args=$matches[1]', 'top');

        add_rewrite_rule('shop/category/mentors/([^/]+)/?', 'index.php?tpp_pagename=tpp_mentors&category_slug=$matches[1]', 'top');

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

            case 'tpp_mentor';

                $this->renderMentor();
                break;

            case 'tpp_mentor_upload':
                $this->upload();
                break;

            default:
                //do nothing
                break;
        }


    }

    public function delete()
    {

        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to delete this mentor.');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToLogin();
        }

        if (false === ($store = TppStoreControllerStore::getInstance()->loadStoreFromSession())) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to delete this mentor.');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToLogin();
        }


        $mentor_id = filter_input(INPUT_POST, 'm', FILTER_SANITIZE_NUMBER_INT);

        if (intval($mentor_id) < 1) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to delete this mentor.');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToDashboard('mentors/');
        }


        $mentor = $this->getMentorModel()->setData(array(
            'mentor_id'    =>  $mentor_id
        ))->getMentorById();

        if (intval($mentor->mentor_id) < 1 || $mentor->store_id != $store->store_id) {
            TppStoreMessages::getInstance()->addMessage('error', 'You are not authorised to delete this product.');
            TppStoreMessages::getInstance()->saveToSession();
            $this->redirectToDashboard('products/');
        }



        if (true === $mentor->delete()) {
            TppStoreMessages::getInstance()->addMessage('message', $mentor->mentor_name . ' deleted!');
        }

        TppStoreMessages::getInstance()->saveToSession();

        $this->redirectToDashboard('mentors/');


    }

    private function renderMentor()
    {
        $mentor_slug = get_query_var('args');

        $mentor_slug = trim($mentor_slug);

        if ($mentor_slug == '') {
            $this->_setWpQuery404();
            include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
            exit;
        }

        $mentor = $this->getMentorModel()->getMentorBySlug($mentor_slug);

        if (intval($mentor->mentor_id) == 0) {
            $this->_setWpQuery404();
            include TPP_STORE_PLUGIN_DIR . 'site/views/404.php';
        } else {



            $page = get_query_var('paged');

            if (intval($page) <= 0) {
                $page = 1;
            }

            $this->pageTitle('Mentor - ');

            $this->pageTitle($mentor);

            $this->setPageDescription($mentor);

            $cacher = new TppCacher();
            $cur = geo::getInstance()->getCurrency();

            if (wp_is_mobile() && !tpp_is_tablet()) {
                $cacher->setCachePath('mentor/' . $mentor->mentor_id . '/mobile/' . $cur . '/page_' . $page . '/');
            } else {
                $cacher->setCachePath('mentor/' . $mentor->mentor_id . '/desktop/' . $cur . '/page_' . $page . '/');
            }

            $cacher->setCacheName('mentor');
            unset($cur);

            wp_enqueue_style('store', '/assets/css/store.css');

            if (false === ($html = $cacher->readCache(-1))) {


                $mentors_model = $this->getMentorsModel()->setData(array(
                    'store_id'  =>  $mentor->store_id
                ));

                $products = $mentors_model->getMentorSessionsByMentor($mentor->mentor_id, $page, 20, 1);

                $total = $mentors_model->getMentorSessionCountByMentor($mentor->mentor_id, 1);

                ob_start();
                include TPP_STORE_PLUGIN_DIR . 'site/views/mentor.php';
                $html = ob_get_contents();
                $cacher->saveCache($html);
                ob_end_clean();

            }

            get_header();
            echo $html;
            get_footer();


        }
        exit;

    }

    private function renderList()
    {
        $page = get_query_var('paged')?:1;

        $args = get_query_var('args');


        //determine the category level
        $slug = get_query_var('category_slug');

        $slug = trim($slug);

        $level = 1;

        if ($slug == '' || $slug == 'mentors') {
            $slug = 'mentors';
            $tmp = $slug;
        } else {
            $slug = trim($slug);

            if ($slug !== '') {

                $slug = 'mentors/' . $slug;

                $tmp = explode('/', $slug);

                $level = count($tmp);

                //get the count of slashes - this indicates the level.

                if (substr($slug, -1) == '/') {
                    $slug = substr($slug, 0, -1);
                }

                if (false !== strpos($slug, '/')) {
                    $slug = substr($slug, strrpos($slug, '/') + 1);
                }
            }
        }

        $category = $this->getCategoryModel()->getCategoryBySlug($slug, $level);


        $this->pageTitle($tmp);

        if (intval($page) > 1) {
            $this->pageTitle(': page ' . $page);
        }

        if (trim($args) != '') {
            TppStoreHelperHtml::getInstance()->block_search_engines = true;
        }

        switch ($args) {

            case 'a-z':
            default:
                $mentors = $this->getMentorsModel()->getMentors($page, 'm.mentor_name', 'ASC', $category->category_id);
                break;

            case 'z-a':
                $mentors = $this->getMentorsModel()->getMentors($page, 'm.mentor_name', 'DESC', $category->category_id);
                break;

            case 'highest-price':
                $mentors = $this->getMentorsModel()->getMentors($page, 'p.price', 'ASC', $category->category_id);
                break;
            case 'lowest-price':
                $mentors = $this->getMentorsModel()->getMentors($page, 'p.price', $category->category_id);
                break;

            case 'lowest-rated':
                $mentors = $this->getMentorsModel()->getMentors($page, 'rating', 'ASC', $category->category_id);

                break;
        }

        if (count($mentors) > 0) {
            $mentor_ids = array();

            foreach ($mentors as $mentor) {
                $mentor_ids[] = $mentor->mentor_id;
            }

            unset($mentor);

            $specialisms = $this->getMentorSpecialismsModel()->getSpecialismsByMentors($mentor_ids);
            unset($mentor_ids);

            foreach ($mentors as $mentor) {
                if (isset($specialisms[$mentor->mentor_id])) {
                    $mentor->getSpecialism(false)->setSpecialisms($specialisms[$mentor->mentor_id]);
                }
            }

            unset($specialisms);
            unset($mentor);

        }

        //get the specialisms for the select mentors!

        include TPP_STORE_PLUGIN_DIR . 'site/views/mentors.php';

        exit;
    }

    private function renderSessionList()
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


    private function upload()
    {

        if (false === ($user = TppStoreControllerUser::getInstance()->loadUserFromSession())) {
            $this->_exitStatus('You are not authorised', true);
        }


        if (false === ($store = TppStoreControllerStore::getInstance()->loadStoreFromSession())) {
            //load the temp path

            $this->_exitStatus('Could not determine your store. Please logout and login again.', true);

        }



        if (false === ($save_path = $this->loadMentorUploadSession())) {
            $this->_exitStatus('No upload path set', true);
        }

        $save_path = $save_path['path'];

        if (!class_exists('TppStoreLibraryFileImage')) {
            include TPP_STORE_PLUGIN_DIR . 'libraries/file/image.php';
        }

        //create the save path if it doesn't exist:

        $directory = new TppStoreDirectoryLibrary();

        $directory->setDirectory($save_path);

        if (!$directory->createDirectory()) {
            $this->_exitStatus('Unable to create the save directory', true);
        }

        $image = new TppStoreLibraryFileImage();

        if (isset($_FILES['pic'])) {
            $image->setUploadedFile($_FILES['pic']);

            if (false === $image->validateUploadedFile($image::$image_mime_type)) {
                $this->_exitStatus($image->getError(), true);
            }

            //move the uploaded file

            if (false === $image->moveUploadedFile($save_path)) {
                $this->_exitStatus($image->getError(), true);
            }



        } else {
            $image->createImageFromInput($save_path);
        }

        $image->setFile($save_path . $image->getUploadedName());

        $image->resize(TppStoreModelProductImages::getSize('thumb'));

        $this->_exitStatus('success', false, array(
            'saved_name'    =>  $image->getUploadedName()
        ));

        exit;
    }

    public function renderMentorForm(TppStoreModelStore $store)
    {

        $this->_setUncachedHeader();

        //get the mentor id?

        $mentor_id = get_query_var('mentor_id');

        $mentor = $this->getMentorModel();

        if (intval($mentor_id) > 0) {
            $mentor->setData(array(
                'mentor_id' =>  $mentor_id
            ))->getMentorById();
        }

        if (false !== $mentor->readFromPost()) {

            if (false !== $mentor->save()) {
                $this->destroyMentorUploadSession();
                TppStoreMessages::getInstance()->addMessage('message', 'Mentor saved, now <a class="btn btn-primary" href="/shop/dashboard/mentor_session/new/' . $mentor->mentor_id . '/">add a session</a>');
                TppStoreMessages::getInstance()->saveToSession();
                $this->redirectToDashboard('mentors');
            }
        }


        if (intval($mentor_id) > 0 && $mentor->store_id != $store->store_id) {
            $this->redirectToDashboard();
        }



        $this->saveMentorUploadSession($store->store_id, $mentor->mentor_id);


        wp_enqueue_script('mentor_dashboard', TPP_STORE_PLUGIN_URL . '/site/assets/js/dashboard/mentor-ck.js', array('jquery'), 1, true);
        include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/mentor/form.php';


    }

    public function saveMentorUploadSession($store_id = 0, $mentor_id = 0)
    {

        if (!session_id()) {
            ob_start();
            session_start();
            ob_end_clean();
        }

        if (intval($mentor_id) == 0) {

            if (!isset($_SESSION['tpp_store_mentor_path']) || (isset($_SESSION['tpp_store_mentor_path']) && intval($_SESSION['tpp_store_mentor_path']['directory']) != 0)) {
                $mentor_id = uniqid('new_mentor');
            } else {
                $mentor_id = $_SESSION['tpp_store_mentor_path']['directory'];
            }

        }

        if (!isset($_SESSION['tpp_store_mentor_path'])) {
            $_SESSION['tpp_store_mentor_path'] = array();
        }
        $_SESSION['tpp_store_mentor_path']['path'] = WP_CONTENT_DIR . '/uploads/store/' . $store_id . '/mentor/' . $mentor_id;

        $_SESSION['tpp_store_mentor_path']['directory'] = $mentor_id;

    }

    public function loadMentorUploadSession()
    {
        if (!session_id()) {
            ob_start();
            session_start();
            ob_end_clean();
        }

        return isset($_SESSION['tpp_store_mentor_path'])?$_SESSION['tpp_store_mentor_path']:false;
    }

    public function destroyMentorUploadSession()
    {
        if (!session_id()) {
            ob_start();
            session_start();
            ob_end_clean();
        }

        $mentor_id = $_SESSION['tpp_store_mentor_path']['directory'];

        if (intval($mentor_id) == 0) {
            //destroy the temporary folder!
            $directory = new TppStoreDirectoryLibrary();

            $directory->setDirectory($_SESSION['tpp_store_mentor_path']['path']);

            $directory->deleteDirectory();

        }

        $_SESSION['tpp_store_mentor_path'] = null;
        unset($_SESSION['tpp_store_mentor_path']);
    }



}