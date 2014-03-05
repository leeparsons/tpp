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
                <li class="first <?php echo $url == '/shop/myaccount/'?'active':'' ?>">
                    <a class="primary" href="/shop/myaccount/">Account</a>
                </li>
                <li class="<?php echo $url == '/shop/myaccount/profile/edit'?'active':'' ?>">
                    <a class="primary" href="/shop/profile/<?php echo $user->user_id ?>">Profile</a>
                    <a class="btn btn-primary" href="/shop/profile/<?php echo $user->user_id ?>">View Profile</a>
                    <a href="/shop/myaccount/profile/edit" class="btn btn-primary">Edit Profile</a>
                </li>

                <li>
                    <?php if (false !== $store && intval($store->store_id) > 0): ?>
                        <a class="primary" href="/shop/myaccount/store/">Store Application</a>
                    <?php else: ?>
                        <a class="primary" href="/shop/sell-with-us/">Store Application</a>
                    <?php endif; ?>
                </li>

                <li class="<?php echo $url == '/shop/myaccount/purchases/'?'active':'' ?>">
                    <a class="primary" href="/shop/myaccount/purchases/">Purchases (<?php echo TppStoreControllerDashboard::getInstance()->getPurchaseCount($user); ?>)</a>
                </li>

                <li class="<?php echo $url == '/shop/myaccount/messages/'?'active':'' ?>">
                    <a class="primary" href="/shop/myaccount/messages/">Messages (<?php echo TppStoreControllerDashboard::getInstance()->getUnreadMessagesCount($user) ?> new)</a>
                </li>
            </ul>
        </nav>


    </aside>

    <?php wp_enqueue_script('dashboard-sidebar', TPP_STORE_PLUGIN_URL . '/site/assets/js/dashboard/side-menu-ck.js', array('jquery'), 3.5, true); ?>
</div>
