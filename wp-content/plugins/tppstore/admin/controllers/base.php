<?php
/**
 * User: leeparsons
 * Date: 14/03/2014
 * Time: 17:35
 */
 
class TppStoreAbstractAdminBase extends TppStoreAbstractBase {

    public function getAdminProductsModel()
    {
        return new TppStoreModelAdminProducts();
    }

    public function getAdminCategoriesModel()
    {
        return new TppStoreModelAdminCategories();
    }
}