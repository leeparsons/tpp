<?php
/**
 * User: leeparsons
 * Date: 30/12/2013
 * Time: 12:07
 */
 
class TppStoreAdminControllerDefault extends TppStoreAbstractAdminBase {


    public static function renderDashboard()
    {
        include TPP_STORE_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    public function renderProductsMenu()
    {
        include TPP_STORE_PLUGIN_DIR . 'admin/views/menu/products.php';
    }

    public function renderBannerList()
    {
        wp_enqueue_script('jquery-ui-sortable');

        $banners = $this->getAdminBannersModel()->getAllBanners();

        include TPP_STORE_PLUGIN_DIR . 'admin/views/banners/homepage.php';

    }

    public function saveBannerOrdering()
    {

        $ordering = filter_input(INPUT_POST, 'ordering');

        $ordering = explode(':', $ordering);

        $model = $this->getAdminBannersModel();

        if (false !== $model->reorder($ordering)) {
            $this->_exitStatus('success', '');
        } else {
            $this->_exitStatus('fail');
        }

    }

    public function saveBanner()
    {
        $nonce = filter_input(INPUT_POST, 'save_banner', FILTER_SANITIZE_STRING);

        if (!wp_verify_nonce($nonce, 'save_banner')) {
            exit('you are not authorised to complete this action.');
        }

        $banner = $this->getBannerModel();

        $banner->readFromPost();

        if (true === $banner->save()) {
            TppStoreMessages::getInstance()->addMessage('message', 'Banner Saved');

        }


        TppStoreMessages::getInstance()->saveToSession();

        if (intval($banner->banner_id) > 0) {
            $this->redirect(admin_url('admin.php?page=tpp-store-banner&id=' . $banner->banner_id));
        } else {
            $this->redirect($_POST['_wp_http_referer']);
        }



    }



    public function renderBannerForm()
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        $banner = $this->getBannerModel();

        if (intval($id) > 0) {
            $banner->setData(array('banner_id' =>  $id))->getBanner();
        }


        include TPP_STORE_PLUGIN_DIR . 'admin/views/banners/form.php';

    }
}