<?php

add_action('admin_menu', function() {
    add_menu_page('Store', 'Store', 'edit_pages', 'tpp-store',
        array(
            'TppStoreAdminControllerDefault',
            'renderDashboard'
        )
    );
    add_submenu_page('tpp-store', 'Categories', 'Featured Categories', 'edit_pages', 'tpp-store-categories',
        array('TppStoreAdminControllerCategories', 'renderCategories')
    );

    add_submenu_page('tpp-store-category', 'Category', NULL, 'edit_pages', 'tpp-store-category',
        array(
            'TppStoreAdminControllerCategories',
            'renderCategory'
        )
    );
    add_submenu_page('tpp-store', 'Store Applications', 'Store Applications', 'edit_pages', 'tpp-store-approvals', function() {
        TppStoreAdminControllerStore::getInstance()->renderApplicationList();
    });

    add_submenu_page('tpp-store', NULL, NULL, 'edit_pages', 'tpp-store-application', function() {
        TppStoreAdminControllerStore::getInstance()->renderApplication();
    });
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



function installTppStore() {
    //do nothign right now
}