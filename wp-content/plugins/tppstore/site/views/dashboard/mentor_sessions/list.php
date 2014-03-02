<?php
/**
 * User: leeparsons
 * Date: 13/02/2014
 * Time: 23:53
 */


get_header(); ?>


<header>
    <h1><?php echo $mentor->mentor_name ?>'s Mentor Sessions</h1>
</header>
<?php TppStoreMessages::getInstance()->render() ?>
    <div class="wrap">
        <a class="btn btn-primary" href="/shop/dashboard/mentor_session/new/<?php echo $mentor->mentor_id ?>">Create a session</a>
        <a href="/shop/dashboard/mentors" class="btn btn-default">Cancel</a>
        <br><br>
    </div>
<?php if ($mentor_session_count > 0): ?>

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
            <?php foreach ($mentor_sessions as $product): ?>
                <?php $edit_url = "/shop/dashboard/mentor_session/edit/" . $product->product_id; ?>
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

    <p>No Mentor Sessions Found</p>
<?php endif;


get_footer();