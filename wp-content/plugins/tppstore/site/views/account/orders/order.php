<?php
/**
 * User: leeparsons
 * Date: 24/01/2014
 * Time: 22:48
 */

include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/header.php'; ?>

<article class="page-article order-history">

    <header>
        <h1>My order</h1>
    </header>

    <?php if (count($order_items) > 0 ): ?>
        <div class="wrap">
            <h3>Items (<?php echo count($order_items) ?>)</h3>

            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Store</th>
                        <th>Type</th>
                        <th>Cost</th>
                        <th>Extra Information</th>
                    </tr>
                </thead>
            <tbody>

            <?php

            foreach ($order_items as $product):

                $product->getData();

                ?>
                <tr>
                    <td><?php echo $product->product_title ?></td>
                    <td>

                        <?php

                        //echo $products[$product->product_id]->getStore()->store_name

                        echo $product->getStore()->store_name

                        ?>

                    </td>
                    <td><?php

                        //echo $products[$product->product_id]->getProductType()
                        echo $product->getProductType()

                        ?></td>
                    <td><?php

                        echo $product->getFormattedPrice(true);

                        ?></td>
                    <td><?php if ($product->product_type == 2): ?><a href="<?php echo $product->getDownloadUrl() ?>">Download</a><?php else: ?>N/A<?php endif; ?></td>
                </tr>



            <?php endforeach; ?>
</tbody>
                </table>

        </div>

    <?php endif; ?>

    <div class="half-left">
        <h3>Order Information</h3>

        <div class="form-group">
            <span>Date</span>
            <span class="form-control"><?php echo $order->getOrderDate(true) ?></span>
        </div>

        <div class="form-group">
            <span>Reference</span>
            <span class="form-control"><?php echo $order->order_id ?></span>
        </div>

        <div class="form-group">
            <span>Status</span>
            <span class="form-control"><?php echo $order->status ?></span>
        </div>

        <div class="form-group">
            <span>Total</span>
            <span class="form-control"><?php echo $order->getFormattedTotal() ?></span>
        </div>

        <?php if ($order->discount > 0): ?>

        <div class="form-group">
            <span>Discount</span>
            <span class="form-control"><?php echo $order->getFormattedTotal($order->discount) ?></span>
        </div>

        <?php endif; ?>

        <?php if ($order->tax_rate > 0): ?>

            <div class="form-group">
                <span>Tax</span>
                <span class="form-control"><?php echo $order->getFormattedTotal($order->tax_rate * $order->total) ?></span>
            </div>

        <?php endif; ?>




    </div>

    <?php if ($order->status != 'cancelled' && count($payments) > 0): ?>

    <div class="half-right">

        <h3>Payment Information</h3>

        <?php if (count($payments) > 1): ?>
            <pre>You attempted several payments for this order:</pre>
        <?php endif; ?>

        <?php foreach($payments as $payment): ?>

            <div class="form-group">
                <span>Payment Date</span>
                <span class="form-control"><?php echo $payment->getPaymentDate() ?></span>
            </div>

            <div class="form-group">
                <span>Status</span>
                <span class="form-control"><?php echo $payment->status ?></span>
            </div>

            <div class="form-group">
                <span>Amount</span>
            </div>
                <span class="form-control"><?php echo $payment->getFormattedTotal() ?></span>

        <?php endforeach; ?>

    </div>
    <?php endif; ?>

</article>

<?php include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/footer.php';