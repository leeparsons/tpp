<?php
/**
 * User: leeparsons
 * Date: 05/12/2013
 * Time: 19:26
 */

include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/header.php';

?>
<article class="page-article-part">

    <header><h1>My <?php echo $type ?></h1></header>

    <?php

    TppStoreMessages::getInstance()->render();

    if (false === filter_var($store->paypal_email, FILTER_VALIDATE_EMAIL)): ?>

        <pre>You need to complete your payment options on your store before you can start adding products, mentors or events</pre>
    <?php else: ?>



    <?php if (
        count($products) > 0
    ): ?>

        <?php

        include TPP_STORE_PLUGIN_DIR . 'helpers/paginator.php';

        $paginator = new TppStoreHelperPaginator();

        $paginator->total_results = $product_count;

        echo $paginator->render();


        ?>

        <table class="dashboard-list" id="dashboard_list">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th>Title</th>
                    <th>Quantity Available</th>
                    <th>Price</th>
                    <th>Live</th>
                    <th>Type</th>
                    <th>Delete</th>
                </tr>
            </thead>

            <tbody id="dashboard_list_body">
            <?php foreach ($products as $product): ?>
                <?php $edit_url = "/shop/dashboard/" . substr($part, 0, -1) . "/edit/" . $product->product_id; ?>
                <tr class="row" data-target="<?php echo $edit_url ?>">
                    <td>
                    <a href="<?php echo $edit_url ?>"><?php

                        echo $product->getProductImage()->getSrc('cart_thumb', true)

                        ?></a>
                    </td>
                    <td>
                        <a href="<?php echo $edit_url ?>"><?php echo $product->product_title; ?></a>
                    </td>
                    <td>
                        <a href="<?php echo $edit_url ?>"><?php echo intval($product->unlimited) == 1?'unlimited':$product->quantity_available ?></a>
                    </td>
                    <td>
                        <a href="<?php echo $edit_url ?>"><?php echo $product->getFormattedPrice(true); ?></a>
                    </td>
                    <td>
                        <a href="<?php echo $edit_url ?>"><?php echo intval($product->enabled) == 1?(intval($store->enabled) == 1?'Yes':'Store not live'):'No'  ?></a>
                    </td>
                    <td>
                        <a href="<?php echo $edit_url ?>"><?php echo $product->getProductType(); ?></a>
                    </td>
                    <td>
                        <form method="post" action="/shop/dashboard/product_delete/">
                            <input type="hidden" name="p" value="<?php echo $product->product_id ?>">
                            <input type="submit" value="Delete" class="btn btn-danger">
                        </form>

                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>

    <?php else: ?>
        <p>Get started: </p>

        <a href="/shop/dashboard/<?php echo substr($part, 0, -1) ?>/new" class="btn btn-primary">create your first <?php echo strtolower(substr($type, 0, -1)) ?></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/shop/dashboard" class="btn btn-default">Cancel</a>

    <?php endif; ?>

    <?php endif; //end validate email ?>
</article>


<?php include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/footer.php';
