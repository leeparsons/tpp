<?php
/*
 * Template Name: Home Page
 */

get_header();


    $cacher = new TppCacher();
    $cacher->setCachePath('homepage/products/top/' . strtolower(geo::getInstance()->getCurrency()));
    $cacher->setCacheName('top_products');

    if (false === ($html = $cacher->readCache(-1))): ?>
        <?php
        ob_start();
        $products = TppStoreControllerProduct::getInstance()->getTopProducts(); ?>
        <?php if (count($products) > 0): ?>
            <header id="highlighted-products">
                <h2 class="featured">Our Top Picks</h2>
            </header>
            <section class="wrap">
            <ul class="item-list">
                <?php $i = 1; ?>
                <?php foreach ($products as $product): ?>
                    <li class="item-box<?php echo $i%4?'':' last' ?>">
                        <a href="<?php echo $product->getPermalink() ?>">
                            <?php echo $product->getProductImage()->getSrc('thumb', true) ?>
                            <span class="strong"><?php echo $product->getShortTitle() ?></span>
                            <span class="price"><?php echo $product->getFormattedPrice(true) ?></span>
                        </a>
                        <a class="store-tag" href="<?php echo $product->getStore()->getPermalink(); ?>"><?php echo $product->getStore()->store_name ?></a>
                        <?php $i++; ?>
                    </li>
                <?php endforeach; flush(); ?>
            </ul>
        <?php endif; ?>
    <?php
        unset($products);
        unset($product);

    $html = ob_get_contents();

    $cacher->saveCache($html);

    ob_end_clean();


    endif;

    echo $html;

    unset($html);

flush();

    $cacher->setCachePath('homepage/categories/featured/');
    $cacher->setCacheName('categories');

    if (false === ($html = $cacher->readCache(-1))):

        ob_start();


        $categories = TppStoreControllerCategory::getInstance()->getFeaturedCategories();

        if (count($categories) > 0): ?>
        </section>
        </div>

        <div class="wrap wrapspacer wrap-grey">
    <header>
        <h2 class="featured">Featured Categories</h2>
    </header>
    <ul id="featured_categories" class="item-list">
        <?php $i = 1; ?>
        <?php foreach ($categories as $category): ?>
            <li class="item-box<?php echo $i%4?'':' last' ?>">
                <a href="<?php echo $category->getPermalink(); ?>">
                    <img src="<?php echo $category->getImageSrc() ?>" alt="<?php echo $category-category_name ?>">
                    <strong><?php echo $category->category_name; ?></strong>
                    <span><?php echo $category->product_count ?> product<?php echo $category->product_count == 1?'':'s' ?></span>
                </a>
            </li>
            <?php $i++ ?>
        <?php endforeach; ?>
    </ul>
            </section>
        </div>
<?php
            endif;
        unset($categories);

        unset($category);
    $html = ob_get_contents();

        $cacher->saveCache($html);

    ob_end_clean();



    endif; //end cache

echo $html;

unset($html);




//featured blog posts

$cacher->setCachePath('homepage/blog/');
$cacher->setCacheName('posts');

if (false === ($html = $cacher->readCache(-1))) {

    ob_start();

    query_posts('posts_per_page=2');

    if (have_posts()):

        ?>
        </section>
        </div>
        <div class="wrap blk wrapspacer">
        <section class="innerwrap">

        <section id="featured_blog_posts">
        <header><h2 class="featured">Latest Blog Posts</h2></header><?php

        $class = 'align-left';

        while (have_posts()):

            the_post();

            $thumb = get_the_post_thumbnail(get_the_ID(), 'featured_blog_post');

            if ($thumb == '') {
                continue;
            }

            ?><article class="wrap">

            <a class="img <?php echo $class ?>" href="<?php the_permalink() ?>"><?php echo $thumb; ?></a>

            <a class="title" href="<?php the_permalink() ?>">
                <span class="strong"><?php the_title(); ?></span>
            </a>
            <div class="excerpt">
                <?php the_excerpt(); ?>
            </div>
            </article>
            <div class="clear"></div>
            <?php

            $class = 'align-right';
        endwhile;


        ?>
        <a href="/blog" class="align-right btn btn-primary">View Blog</a>
        </section><?php

    endif;

    $html = ob_get_contents();

    ob_end_clean();

    $cacher->saveCache($html);

}

echo $html;
unset($html);
unset($cacher);

get_footer();