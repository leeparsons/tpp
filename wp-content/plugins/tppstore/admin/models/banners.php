<?php
/**
 * User: leeparsons
 * Date: 03/04/2014
 * Time: 08:07
 */
 
class TppStoreModelAdminBanners extends TppStoreAbstractModelResource {



    protected $_table = 'shop_banners';



    public function getEnabledBanners()
    {
        global $wpdb;

        $wpdb->query(
            "SELECT * FROM " . $this->getTable() . " WHERE enabled = 1 ORDER BY ordering"
        );

        $banners = array();

        if ($wpdb->num_rows > 0) {
            foreach ($wpdb->last_result as $i => $row) {
                $banners[$i] = new TppStoreModelBanner();
                $banners[$i]->setData($row);
            }
        }

        return $banners;
    }

    /*
     * Used in the admin only
     */
    public function getAllBanners()
    {
        global $wpdb;


        $wpdb->query(
            "SELECT * FROM " . $this->getTable() . " ORDER BY ordering"
        );


        $banners = array();

        if ($wpdb->num_rows > 0) {
            foreach ($wpdb->last_result as $row) {
                $banners[$row->banner_id] = new TppStoreModelBanner();
                $banners[$row->banner_id]->setData($row);
            }
        }

        return $banners;
    }


    /*
     * Takes an array of ids and recofigures the banners order in the database accordingly
     */
    public function reorder($ids = array())
    {

        if (is_array($ids) && !empty($ids)) {

            global $wpdb;

            foreach ($ids as $x => $id) {
                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE " . $this->getTable() . " SET ordering = %d WHERE banner_id = %d",
                        array(
                            $x+1,
                            $id
                        )
                    ),
                    OBJECT_K
                );

                if ($wpdb->result === false) {
                    return false;
                }

            }

            $this->clearCache();

            return true;

        } else {
            return false;
        }

    }


    private function clearCache()
    {
        $cache = new TppCacher();
        $cache->setCacheName('banners');
        $cache->setCachePath('homepage/banners');
        $cache->deleteCache();
    }
}