<?php
/**
 * User: leeparsons
 * Date: 17/03/2014
 * Time: 12:51
 */



/*
 * provides a meta box to add tags which will be used for product searches on the related products for a blog post
 */

class TppBlogProductRelations extends TppStoreAbstractBase {

    public function save(  )
    {
        global $post;

        $post_id = $post->ID;

        /*
                 * We need to verify this came from the our screen and with proper authorization,
                 * because save_post can be triggered at other times.
                 */

        // Check if our nonce is set.
        if ( ! isset( $_POST['tpp_blog_2_products_nonce'] ) )
            return $post_id;

        $nonce = $_POST['tpp_blog_2_products_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'tpp_blog_2_products_save' ) )
            return $post_id;

        // If this is an autosave, our form has not been submitted,
        //     so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;

        // Check the user's permissions.
        if ( 'post' != $_POST['post_type'] ) {


                return $post_id;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }

        /* OK, its safe for us to save the data now. */

        //get all the product ids and save them!


        $favourites = $this->getAdminFavouritesModel();
        if (true === $favourites->readFromPost()) {
            $favourites->setData(array(
                'related_parent_id' =>  $post_id,
                'position'          =>  'post_related'
            ))->save();
        }


    }

    public function registerAdminHooks()
    {

        add_action('save_post', function() {
            TppBlogProductRelations::getInstance()->save();
        });

        add_action( 'load-post.php', function() {
            TppBlogProductRelations::getInstance()->registerMetaBox();
        } );

        add_action( 'load-post-new.php', function() {
            TppBlogProductRelations::getInstance()->registerMetaBox();
        } );

        add_action(
            'wp_ajax_find_b2p_products',
            function() {
                TppBlogProductRelations::getInstance()->searchProducts();
            }
        );

    }



    public function searchProducts()
    {
        $this->_setJsonHeader();

        $search = filter_input(INPUT_POST, 's', FILTER_SANITIZE_STRING);

        if (trim($search) != '') {
            $products = $this->getAdminProductsModel()->search($search);
        } else {
            $products = array();
        }

        if (count($products) > 0) {
            $this->_exitStatus('success', false,
                  $products
            );
        } else {
            $this->_exitStatus('failed', true);
        }

    }

    public function registerMetaBox()
    {
        add_meta_box(
            'related_product_tags',
            'Related Products',
            function() {
                TppBlogProductRelations::getInstance()->renderMetaBox();
            },
            'post'
        );


    }



    public function renderMetaBox()
    {

        global $post;
        $post_id = $post->ID;

        if (intval($post_id) > 0) {
            $favourites = $this->getAdminFavouritesModel();

            $products = $favourites->setData(array(
                'position'          =>  'post_related',
                'related_parent_id' =>  $post_id
            ))->getDirectFavouriteProducts();
        } else {
            $products = array();
        }

        wp_nonce_field('tpp_blog_2_products_save', 'tpp_blog_2_products_nonce');

        wp_enqueue_script('blog2product', '/assets/js/admin/blog2products.js', array('jquery'), false, true);

        ?>

        <style>
            #related_product_list a {
                margin-right:10px;
                padding:0 5px;
                border:1px solid #000000;
                color:#FFFFFF;
                border-radius:8px;
                min-width:10px;
            }
            #related_product_list a.add {
                background: #4ac51b;
            }
            #related_product_list a.remove {
                background: #ff6f6d;
            }

            #related_product_list {
                max-height:400px;
                overflow:scroll;
                border:1px solid #e5e5e5;
            }

            .remove-product {
                margin-left:10px;
            }

            .remove-product img {
                width:10px;
            }

            #selected_products li {
                border-bottom:1px dashed #c5c5c5;
                margin-bottom:10px;
                padding-bottom:10px;
            }

            #related_product_list li {
                border-bottom:1px dashed #c5c5c5;
                margin-bottom:10px;
                padding:10px;
            }
        </style>
        <label for="blog2product_tags">Enter a search term to find relevant products</label>
        <input type="text" name="blog2product_search" id="blog2product_search">
        <a href="#" id="blog2product_find" class="button button-primary">Find Products</a>
        <img src="/assets/images/ajax-loader-spinner.gif" style="display: none;width:20px;">


        <p style="padding:10px;width:90%;color:#750000;background:#fafafa;height:40px;line-height: 40px;box-shadow: 0px 0px 8px #e5e5e5">
            Warning: these will not save when saving a draft!
        </p>



        <div id="selected_products"><?php

            if (!empty($products)) {
                foreach ($products as $product): ?>
                    <span style="display:block;" id="product_wrap<?php echo $product->product_id ?>"><input type="hidden" name="product[]" value="<?php echo $product->product_id ?>"><?php echo $product->product_title . ': ' . $product->store_name ?><a href="#" class="remove-product"><img src="/assets/images/cross.png"></a></span>
                <?php endforeach;
            }


            ?></div>
        <ul style="display:none" id="related_product_list">

        </ul>
    <?php


    }


}