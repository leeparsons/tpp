<?php

$url = $_SERVER['REQUEST_URI'];

if (!isset($product_count)) {
    $product_count = 0;
}

if (!isset($mentor_sessions_count)) {
    $mentor_sessions_count = 0;
}

?>
<aside class="sidebar-dashboard">
    <nav>
        <ul class="dashboard-menu">
            <li class="first <?php echo $url == '/shop/dashboard'?'active':'' ?>">
                <a class="primary" href="/shop/<?php echo $user->user_type == 'store_owner'?'dashboard':'myaccount' ?>"><?php echo $user->user_type == 'store_owner'?'My Dashboard':'My Account'; ?></a>
            </li>
            <li class="<?php echo $url == '/shop/myaccount/profile/edit'?'active':'' ?>">
                <a class="primary" href="/shop/profile/<?php echo $user->user_id ?>">My Profile</a>
                <a class="btn btn-primary" href="/shop/profile/<?php echo $user->user_id ?>">View Profile</a>
                <a href="/shop/<?php echo $user->user_type == 'store_owner'?'dashboard':'myaccount' ?>/profile/edit" class="btn btn-primary">Edit Profile</a>
            </li>
            <?php if ($user->user_type == 'store_owner'): ?>
                <li class="<?php echo $url == '/shop/dashboard/store'?'active':'' ?>">
                    <a class="primary" href="/shop/dashboard/store">My Store</a>

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
                        <a class="primary" href="/shop/dashboard/products">My Products (<?php echo $product_count; ?>)</a>
                        <a class="btn btn-primary" href="/shop/dashboard/products">View Products</a>
                        <a class="btn btn-primary" href="/shop/dashboard/product/new">Add Product</a>
                    </li>
                    <li class="<?php echo $url == '/shop/dashboard/products'?'active':'' ?>">
                        <a class="primary" href="/shop/dashboard/mentors">My mentor Sessions (<?php echo $mentor_sessions_count; ?>)</a>
                        <a class="btn btn-primary" href="/shop/dashboard/mentors">View Sessions</a>
                        <a class="btn btn-primary" href="/shop/dashboard/mentor/new">Add Mentor Session</a>
                    </li>

                <?php else: ?>
                    <li>
                        <a href="/shop/dashboard/store" class="primary">Products (Create a store first)</a>
                    </li>
                <?php endif; ?>
                <li class="<?php echo $url == '/shop/myaccount/orders'?'active':'' ?>">
                    <a class="primary" href="/shop/<?php echo $user->user_type == 'store_owner'?'dashboard':'myaccount' ?>/orders">My Orders</a>
                </li>
                <li class="<?php echo $url == '/shop/myaccount/messages'?'active':'' ?>">
                    <a class="primary" href="/shop/<?php echo $user->user_type == 'store_owner'?'dashboard':'myaccount' ?>/messages">My Messages</a>
                </li>
            <?php else:

                //determine if this user has a store?

                if (!isset($store) || !($store instanceof TppStoreModelStore)) {
                    $store = new TppStoreModelStore();
                }

                $store->setData(array(
                    'user_id'   =>  $user->user_id
                ))->getStoreByUserID();

                if (intval($store->store_id) > 0): ?>
                <li>
                    <a class="primary" href="/shop/myaccount/store">My Store Application</a>
                </li>
                <?php endif;




                endif; ?>
        </ul>
    </nav>

    <?php if ($user->user_type == 'store_owner'): ?>
    <div class="wrap">

        <pre>Promote your store with a button on your website or blog</pre>

        <a href="/assets/buttons.zip" target="_blank" class="promote"><span></span><img src="/assets/images/dashboard/promote.png" alt="download and promote your store"></a>

    </div>
    <?php endif; ?>

</aside>
<script>
    jQuery(function($) {
        $('.dashboard-menu').find('a:not(.primary)').slideUp();
        $('.dashboard-menu').find('a.primary').each(function() {
            if ($(this).siblings('a').length > 0) {
                $(this).addClass('toggler');

                $(this).on('click', function(e) {
                    e.preventDefault();
                    if (!$(this).siblings('a').eq(0).is(':visible')) {
                        $(this).addClass('active');
                        $(this).siblings('a').slideDown();
                    } else {
                        $(this).removeClass('active');
                        $(this).siblings('a').slideUp();
                    }
                });
            }
        });
    });
</script>