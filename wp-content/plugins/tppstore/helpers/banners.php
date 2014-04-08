<?php


class TppStoreBannersHelper extends TppStoreAbstractBase {


    public function renderBanners()
    {


        $cache = new TppCacher();

        $cache->setCacheName('banners');
        $cache->setCachePath('homepage/banners');

        if (false === ($html = $cache->readCache(-1))) {
            ob_start();
            $banners = $this->getAdminBannersModel()->getEnabledBanners();
            include TPP_STORE_PLUGIN_DIR . 'templates/banners.php';
            $html = ob_get_contents();
            ob_end_clean();
            $cache->saveCache($html);
        }

        echo $html;
    }

}