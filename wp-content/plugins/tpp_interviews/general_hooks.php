<?php

function register_tpp_interview_post_type()
{
    if (is_main_site()) {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }



    if (is_plugin_active('tpp_interviews/tpp_interviews.php')) {


        require TPP_INTERVIEWS_PLUGIN_DIR . 'admin/controllers/default.php';


        register_post_type('tpp_interview',
            array(

            'labels'    =>  array(

                'name'                  =>  __('Interviews'),
                'singular_name'         =>  __('Interview'),
                'menu_name'             =>  __('Interviews'),
                'name_admin_bar'        =>  __('Interviews'),
                'all_items'             =>  __('Interviews'),
                'add_new'               =>  __('Add New Interview'),
                'add_new_item'          =>  __('Add New Interview'),
                'edit_item'             =>  __('Edit Interview'),
                'new_item'              =>  __('New Interview'),
                'view_item'             =>  __('View Interview'),
                'search_items'          =>  __('Interviews'),
                'not_found'             =>  __('No interviews found'),
                'not found in trash'    =>  __('No interviews in trash')

            ),
            'description'   =>  'Interview post',
            'public'        =>  true,
            'supports'      =>  array(
                'title',
                'editor',
                'author',
                'thumbnail',
                'excerpt',
                'custom-fields',
                'comments',
                'revisions'
            ),
            'capability_type' => 'post',
            'taxonomies'    =>  array(
                'post_tag'
            ),
            'rewrite'       =>  array(
                'slug'  =>  'interviews'
            ),
            'menu_position'         =>  5,
            'register_meta_box_cb'  =>  array(
                'TppInterviewsAdminControllerDefault',
                'addMetaBoxes'
            ),
            'has_archive'   =>  true
        ));


        register_taxonomy(
            'interview_topic',
            'tpp_interview',
            array(
                'hierarchical'  =>  true,
                'label'         => 'Interview Topics',
                'labels'        =>  array(
                    'menu_name'     => 'Interview Topics',
                    'add_new_item'  =>  'Add new interview topic'
                ),
                'query_var'     =>  true,
                'rewrite'       =>  array(
                    'slug'  =>  'interview-topic'
                )
            )
        );

        register_taxonomy(
            'interview_category',
            'tpp_interview',
            array(
                'hierarchical'  =>  true,
                'label'         =>  'Interview Categories',
                'labels'        =>  array(
                    'menu_name'     => 'Interview Categories',
                    'add_new_item'  =>  'Add new interview category'
                ),
                'query_var'     =>  true,
                'rewrite'       =>  array(
                    'slug'  =>  'interview-category'
                )
            )
        );

    }

}

add_action('init', 'register_tpp_interview_post_type');