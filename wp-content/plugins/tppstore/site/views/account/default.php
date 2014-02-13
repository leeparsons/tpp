<?php include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/header.php'; ?>

<aside class="aside-75">
    <header>
        <h1>My Account</h1>
    </header>

    <?php TppStoreMessages::getInstance()->render(); ?>


    <ul class="dashboard-icons">
        <?php

        if (intval($store->store_id) > 0): ?>
            <li>
                <a href="/shop/myaccount/store" class="dashboard-store dashboard-icon">
                    <span>My Store</span>
                </a>
            </li>
        <?php endif; ?>

        <li>
            <a href="/shop/myaccount/profile/edit" class="dashboard-profile dashboard-icon">
                <span>My Profile</span>
            </a>
        </li>


    </ul>

</aside>

<?php include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/footer.php'; ?>