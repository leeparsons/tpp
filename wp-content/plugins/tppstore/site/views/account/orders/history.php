<?php
/**
 * User: leeparsons
 * Date: 24/01/2014
 * Time: 21:56
 */

$link = $user->user_type == 'store_owner'?'dashboard':'myaccount';

include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/header.php';


include TPP_STORE_PLUGIN_DIR . 'helpers/paginator.php';

$paginator = new TppStoreHelperPaginator();

$paginator->total_results = $total_orders;



?>

    <article class="page-article">

        <header>
            <h1>My order history</h1>
        </header>

        <?php if (count($orders) > 0): ?>

            <div class="wrap text-right">
                <?php echo $paginator->render(); ?>
            </div>

            <table class="dashboard-list">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Order Date</th>
                        <th>Store</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Order Items</th>
                    </tr>
                </thead>
                <tbody id="dashboard_list_body">
                <?php $stores = array(); ?>
            <?php foreach ($orders as $order): ?>
                <?php $order_url = "/shop/" . $link . "/order/" . $order->order_id; ?>
                <tr data-target="<?php echo $order_url ?>">
                    <td><a href="<?php echo $order_url ?>"><?php echo $order->order_id ?></a></td>
                    <td><a href="<?php echo $order_url ?>"><?php echo $order->getOrderDate() ?></a></td>
                    <td><a href="<?php echo $order_url ?>"><?php echo $order->status ?></a></td>
                    <td><a href="<?php echo $order_url ?>"><?php

                        echo $order->store_name

                        ?></a></td>
                    <td><a href="<?php echo $order_url ?>"><?php echo $order->getFormattedTotal() ?></a></td>
                    <td><a href="<?php echo $order_url ?>"><?php echo $order->count_items ?></a></td></td>
                </tr>
            <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>

            <p>You have no order history</p>

        <?php endif; ?>

    </article>

<?php

include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/footer.php';

