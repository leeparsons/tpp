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
            <h1>Add a Listing</h1>
        </header>

        <?php TppStoreMessages::getInstance()->render(); ?>

        <ul class="dashboard-icons">
            <li>
                <a href="/shop/dashboard/product/new" class="dashboard-product-new dashboard-icon">
                    <span>New Product</span>
                </a>
            </li>

            <li>
                <a href="/shop/dashboard/mentor/new" class="dashboard-mentor-new dashboard-icon">
                    <span>New mentor Session</span>
                </a>
            </li>

        </ul>


    </article>

<?php include 'footer.php';