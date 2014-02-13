<?php
/**
 * User: leeparsons
 * Date: 21/12/2013
 * Time: 08:46
 */
 
get_header(); ?>

<header>
    <h1>Shop</h1>
    <p>All our vendors have their own stores, you can either shop by store or shop by category.</p>
    <a href="" class="btn btn-primary">Shop by Store</a><a href="" class="btn btn-primary">Shop by category</a>
</header>

<div class="wrap" id="stores">
    <?php if (count($stores) > 0): ?>
        <ul class="item-list">
        <?php foreach($stores as $store): ?>
            <li class="item-box">
                <a href="<?php echo $store->getPermalink() ?>">
                <img src="<?php echo $store->getSrc(false, 'thumb') ?>" alt="<?php echo $store->store_name ?>">
                <span><?php echo $store->store_name ?></span>
                <strong>Vendor: <?php echo $store->getOwner() ?></strong>
                </a>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Sorry, all stores are currently closed. Please come back later.</p>
    <?php endif; ?>
</div>
<?php get_footer();