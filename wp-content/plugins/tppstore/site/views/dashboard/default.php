<?php
/**
 * User: leeparsons
 * Date: 05/12/2013
 * Time: 19:16
 */

include 'header.php';

?>

<article class="page-article-part">


    <header>
        <h1>Your Dashboard</h1>
    </header>

    <?php TppStoreMessages::getInstance()->render(); ?>


    <ul class="dashboard-icons">
        <li>
            <a href="/shop/dashboard/event/new" class="dashboard-event-new dashboard-icon">
                <span>New event or workshop</span>
            </a>
        </li>
        <li>
            <a href="/shop/dashboard/mentors/" class="dashboard-mentor-new dashboard-icon">
                <span>New mentor</span>
            </a>
        </li>
        <li>
            <a href="/shop/dashboard/product/new" class="dashboard-product-new dashboard-icon">
                <span>New Product</span>
            </a>
        </li>

        <!--li>
            <a href="/shop/dashboard/mentor_session/new" class="dashboard-mentor-session-new dashboard-icon">
                <span>New mentor Session</span>
            </a>
        </li-->
        <li>
            <a href="/shop/dashboard/orders" class="dashboard-orders dashboard-icon">
                <span>My Orders</span>
            </a>
        </li>
        <li>
            <a href="/shop/dashboard/messages" class="dashboard-messages dashboard-icon">
                <span>My Messages</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $store->getPermalink() ?>" class="dashboard-store dashboard-icon">
                <span>My Store</span>
            </a>
        </li>
        <li>
            <a href="/shop/dashboard/store" class="dashboard-store-edit dashboard-icon">
                <span>Edit Store</span>
            </a>
        </li>



       <?php /*

        <li>
            <a href="/shop/dashboard/products" class="dashboard-products dashboard-icon">
                <span>My Products</span>
            </a>
        </li>


        <li>
            <a href="/shop/dashboard/mentors" class="dashboard-mentors dashboard-icon">
                <span>My mentor Sessions</span>
            </a>
        </li>

        */ ?>

        <li>
            <a href="/shop/profile/<?php echo $user->user_id ?>" class="dashboard-profile dashboard-icon">
                <span>My Profile</span>
            </a>
        </li>

        <li>
            <a href="/shop/myaccount/profile/edit" class="dashboard-profile-edit dashboard-icon">
                <span>Edit Profile</span>
            </a>
        </li>


    </ul>


</article>

<?php include 'footer.php';