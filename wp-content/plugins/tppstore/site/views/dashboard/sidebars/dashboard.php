<div class="aside-25 dashboard">
<?php

$url = $_SERVER['REQUEST_URI'];

if (!isset($product_count)) {
    $product_count = 0;
}

if (!isset($mentor_count)) {
    $mentor_count = 0;
}

if (!isset($event_count)) {
    $event_count = 0;
}

?>
<aside class="sidebar-dashboard">
    <nav>
        <ul class="dashboard-menu">
            <li class="first <?php echo $url == '/shop/dashboard'?'active':'' ?>">
                <a class="primary" href="/shop/dashboard">Dashboard</a>
            </li>
            <li class="<?php echo $url == '/shop/myaccount/profile/edit'?'active':'' ?>">
                <a class="primary" href="/shop/profile/<?php echo $user->user_id ?>">Profile</a>
                <a class="btn btn-primary" href="/shop/profile/<?php echo $user->user_id ?>">View Profile</a>
                <a href="/shop/dashboard/profile/edit" class="btn btn-primary">Edit Profile</a>
            </li>
            <li class="<?php echo $url == '/shop/dashboard/store'?'active':'' ?>">
                <a class="primary" href="/shop/dashboard/store">Store</a>
                <?php if (is_object($store) && intval($store->store_id) > 0): ?>
                    <?php if (intval($store->enabled) == 1): ?>
                        <a href="/shop/dashboard/store/gooffline" class="btn btn-danger">Go Offline</a>
                    <?php else: ?>
                        <a href="/shop/dashboard/store/golive" class="btn btn-go">Go Live</a>
                    <?php endif; ?></a>
                <?php endif; ?>
                <a href="/shop/dashboard/store" class="btn btn-primary"><?php echo (is_object($store) && intval($store->store_id) > 0)?'Edit':'Create' ?> Store</a>
                <a href="/shop/dashboard/store-pages" class="btn btn-primary">Edit Store Pages</a>
            </li>
            <?php if (is_object($store) && intval($store->store_id) > 0): ?>
            <li class="<?php echo $url == '/shop/dashboard/products'?'active':'' ?>">
                <a class="primary" href="/shop/dashboard/products">Products (<?php echo $product_count; ?>)</a>
                <a class="btn btn-primary" href="/shop/dashboard/products">View Products</a>
                <a class="btn btn-primary" href="/shop/dashboard/product/new">Add Product</a>
            </li>
            <li class="<?php echo $url == '/shop/dashboard/products'?'active':'' ?>">
                <a class="primary" href="/shop/dashboard/mentors">Mentors (<?php echo $mentor_count; ?>)</a>
            </li>
            <?php else: ?>
            <li>
                <a href="/shop/dashboard/store/" class="primary">Products (Create a store first)</a>
            </li>
            <?php endif; ?>
            <?php if (is_object($store) && intval($store->store_id) > 0): ?>
            <li>
                <a href="/shop/dashboard/events" class="primary">Events (<?php echo $event_count; ?>)</a>
            </li>
            <?php else: ?>
            <?php $store = new TppStoreModelStore(); ?>
            <li>
                <a href="/shop/dashboard/store/" class="primary">Events (Create a store first)</a>
            </li>
            <?php endif; ?>
            <li class="<?php echo $url == '/shop/myaccount/messages'?'active':'' ?>">
                <a class="primary" href="/shop/<?php echo $user->user_type == 'store_owner'?'dashboard':'myaccount' ?>/messages">Messages (<?php echo TppStoreControllerDashboard::getInstance()->getUnreadMessagesCount($user) ?> new)</a>
            </li>
            <li class="<?php echo $url == '/shop/myaccount/purchases/'?'active':'' ?>">
                <a class="primary" href="/shop/myaccount/purchases/">Purchases (<?php echo TppStoreControllerDashboard::getInstance()->getPurchaseCount($user); ?>)</a>
            </li>
            <li class="<?php echo $url == '/shop/dashboard/orders/'?'active':'' ?>">
                <a class="primary" href="/shop/dashboard/orders/">Received Orders (<?php echo TppStoreControllerDashboard::getInstance()->getOrderCount($store) ?>)</a>
            </li>
        </ul>
    </nav>

    <div class="wrap">

        <pre>Promote your store with a button on your website or blog</pre>

        <a href="/assets/buttons.zip" target="_blank" class="promote"><span></span><img src="/assets/images/dashboard/promote.png" alt="download and promote your store"></a>

    </div>

</aside>
<?php wp_enqueue_script('dashboard-sidebar', TPP_STORE_PLUGIN_URL . '/site/assets/js/dashboard/side-menu-ck.js', array('jquery'), 3.5, true); ?>
</div>
