<?php
/**
 * User: leeparsons
 * Date: 17/02/2014
 * Time: 21:54
 */

include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/header.php'; ?>

    <header>
        <h1>Events</h1>
    </header>
<?php TppStoreMessages::getInstance()->render() ?>
<div class="wrap">
    <a href="/shop/dashboard/event/new/" class="btn btn-primary">Create a new event</a>
</div>

<?php if ($event_count > 0): ?>
    <table class="dashboard-list" id="dashboard_list">
        <thead>
        <tr>
            <th>&nbsp;</th>
            <th>Title</th>
            <th>Quantity Available</th>
            <th>Price</th>
            <th>Live</th>
            <th>Type</th>
            <th>View on site</th>
            <th>Delete</th>
        </tr>
        </thead>

        <tbody id="dashboard_list_body">
        <?php foreach ($products as $product): ?>
            <?php $edit_url = "/shop/dashboard/event/edit/" . $product->product_id; ?>
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
                    <?php if ($product->enabled == 1): ?>
                        <a target="_blank" class="btn btn-primary" href="<?php echo $product->getPermalink(); ?>">View on site</a>
                    <?php endif; ?>
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
    <p>No Events Found</p>
<?php endif; ?>

<?php include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/footer.php';
