<?php

add_action('admin_menu', function() {
    add_menu_page('Store', 'Store', 'edit_pages', 'tpp-store',
        array(
            'TppStoreAdminControllerDefault',
            'renderDashboard'
        )
    );
    add_submenu_page('tpp-store', 'Categories', 'Categories', 'edit_pages', 'tpp-store-categories',
        array('TppStoreAdminControllerCategories', 'renderCategories')
    );

    add_submenu_page('tpp-store', 'Products', 'Products', 'edit_pages', 'tpp-store-products-menu',
        function() {
            TppStoreAdminControllerDefault::getInstance()->renderProductsMenu();
        }
    );


    add_submenu_page(NULL, NULL, NULL, 'edit_pages', 'tpp-store-products',
        function() {
            TppStoreAdminControllerProducts::getInstance()->renderProducts();
        }
    );

    add_submenu_page(NULL, NULL, NULL, 'edit_pages', 'tpp-store-product',
        function() {
            TppStoreAdminControllerProducts::getInstance()->renderProduct();
        }
    );

    add_submenu_page('tpp-store-category', 'Category', NULL, 'edit_pages', 'tpp-store-category',
        array(
            'TppStoreAdminControllerCategories',
            'renderCategory'
        )
    );


    add_submenu_page('tpp-store-category', 'Category', NULL, 'edit_pages', 'tpp-store-category-favourites',
        function() {


            TppStoreAdminControllerCategories::getInstance()->renderCategoryFavourites();
        }
    );

    add_submenu_page(null, null, null, 'edit_pages', 'tpp-store-product-favourites',
        function() {
            TppStoreAdminControllerProducts::getInstance()->homePageFavouritesList();
        }
    );

    add_submenu_page('tpp-store', 'Store Applications', 'Store Applications', 'edit_pages', 'tpp-store-approvals', function() {
        TppStoreAdminControllerStore::getInstance()->renderApplicationList();
    });

    add_submenu_page('tpp-store', NULL, NULL, 'edit_pages', 'tpp-store-application', function() {
        TppStoreAdminControllerStore::getInstance()->renderApplication();
    });

    add_submenu_page('tpp-store', NULL, 'Sidebar Favourites', 'edit_pages', 'tpp-store-sidebar-favourites', function() {
        TppStoreAdminControllerProducts::getInstance()->sidebarFavouritesList();
    });

    add_submenu_page('tpp-store', 'Reports', 'Reports', 'edit_pages', 'tpp-store-reports',
        array(
            'TppStoreAdminControllerReports',
            'renderReports'
        )
    );

    add_submenu_page(NULL, NULL, NULL, 'edit_pages', 'tpp-store-loyalty-report',
        function () {
            TppStoreAdminControllerReports::getInstance()->renderLoyaltyReport();
        }
    );

    add_submenu_page('tpp-store-reports', 'Report', 'Report', 'edit_pages', 'tpp-store-report',
        array(
            'TppStoreAdminControllerReports',
            'renderReport'
        )
    );

    add_submenu_page('tpp-store-best-sellers', 'Report', 'Report', 'edit_pages', 'tpp-store-best-sellers',
        function () {
            TppStoreAdminControllerReports::getInstance()->renderBestSellers();
        }
    );


    add_submenu_page(NULL, NULL, NULL, 'edit_pages', 'tpp-store-banners',
        function() {
            TppStoreAdminControllerDefault::getInstance()->renderBannerList();
        }
    );

    add_submenu_page(NULL, NULL, NULL, 'edit_pages', 'tpp-store-banner',
        function() {
            TppStoreAdminControllerDefault::getInstance()->renderBannerForm();
        }
    );
});

add_action('admin_action_tpp_store_save_ctgy', array(
    'TppStoreAdminControllerCategories',
    'saveTppCategory',
    )
);

add_action('admin_action_tpp_save_application', array(
        'TppStoreAdminControllerStore',
        'saveTppApplication',
    )
);

add_action('admin_action_tpp_save_banner', function() {
        TppStoreAdminControllerDefault::getInstance()->saveBanner();
    }
);


add_action(
    'wp_ajax_tpp_save_banner_ordering', function() {
        TppStoreAdminControllerDefault::getInstance()->saveBannerOrdering();
    }
);

add_action(
    'admin_action_save_category_favourite_products',
    function() {
        TppStoreAdminControllerCategories::getInstance()->saveFavouriteProducts();
    }
);

add_action(
    'admin_action_save_homepage_favourite_products',
    function() {
        TppStoreAdminControllerProducts::getInstance()->saveHomepageProducts();
    }
);

function installTppStore() {
    //do nothign right now
}