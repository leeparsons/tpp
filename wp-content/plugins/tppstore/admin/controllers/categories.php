<?php
/**
 * User: leeparsons
 * Date: 30/12/2013
 * Time: 12:13
 */
 
class TppStoreAdminControllerCategories extends TppStoreAbstractBase {

    public static function renderCategories()
    {


        wp_enqueue_style('tpp_categories', TPP_STORE_PLUGIN_URL . '/admin/assets/css/categories.css');

        $categories = TppStoreModelCategories::getInstance();
        $categories->getCategories(array(
            'heirarchical'  =>  true,
            'product_count' =>  true
        ));



        include TPP_STORE_PLUGIN_DIR . 'admin/views/categories/list.php';

    }


    public function renderCategory()
    {

        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        if (is_null($id)) {
            echo 'Can not find category';
        } else {
            $category = self::getInstance()->getCategoryModel()->setData(array(
                'category_id'   =>  $id
            ));

            $category->getCategoryById($id);

            if (intval($category->category_id) < 1) {
                echo 'Can not find the category';
            } else {
                wp_enqueue_style('tpp_categories', TPP_STORE_PLUGIN_URL . '/admin/assets/css/categories.css');

                include TPP_STORE_PLUGIN_DIR . 'admin/views/categories/edit.php';
            }

        }

    }

    public static function saveTppCategory()
    {

        $nonce = filter_input(INPUT_POST, 'category_nonce', FILTER_SANITIZE_STRING);

        if (!wp_verify_nonce($nonce, 'save_category')) {
            throw new Exception('You are not authorised to make this change to a category');
        }

        $category = self::getInstance()->getCategoryModel();

        $id = filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT);


        $category->getCategoryById($id);

        if (intval($category->category_id) < 1) {
            throw new Exception('You are not authorised to update this category');
        }

        if (!$category->readFromPost()) {
            throw new Exception('You are not authorised to make this change to a category');
        }

        if (!$category->save()) {


            if (TppStoreMessages::getInstance()->getTotal() > 0) {
                $messages = TppStoreMessages::getInstance()->getMessages();

                $message = implode('<br>', $messages);

            } else {
                $message = 'You are not authorised to make this change to a category';
            }

            exit($message);

        }

        TppStoreMessages::getInstance()->addMessage('message', array('saved'    =>  'Category Saved'));

        TppStoreMessages::getInstance()->saveToSession();

        self::getInstance()->redirect($_POST['_wp_http_referer']);

    }

}