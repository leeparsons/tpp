<?php
/**
 * User: leeparsons
 * Date: 24/01/2014
 * Time: 22:48
 */

include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/header.php'; ?>

<article class="page-article order-history">

    <header>
        <h1>My Purchase</h1>
    </header>

    <?php TppStoreMessages::getInstance()->render() ?>

    <?php if (count($order_items) > 0 ): ?>
        <div class="wrap">
            <h3>Items (<?php echo count($order_items) ?>)</h3>

            <table class="dashboard-list">
                <thead>
                    <tr>
                        <th>Item</th>
                        <?php /* <th>Type</th> */ ?>
                        <th>Cost</th>
                        <?php if ($order->discount > 0): ?>
                        <th>Discount</th>
                        <?php endif; ?>
                        <th>Quantity</th>
                        <th>Extra Information</th>
                    </tr>
                </thead>
            <tbody>

            <?php

            foreach ($order_items as $product):

                $product->getData();

                ?>
                <tr>
                    <td style="width:43%">
                        <span><?php echo $product->product_title ?></span>
                        <strong><?php
                            echo $product->getStore()->store_name
                            ?></strong>
                        <?php if ($product->product_type == 1): ?>
                            <br><br>
                        <a class="btn btn-go" style="width:50%" href="<?php echo $product->getDownloadUrl() ?>">Click to Download</a>
                            <br><br>
                        <?php endif; ?>
                    </td>

                    <?php /* <td><span><?php

                        //echo $products[$product->product_id]->getProductType()
                        echo $product->getProductType()

                        ?></span></td>
                    */ ?>
                    <td><span><?php

                            echo $product->getFormattedCurrency(true, $order->currency, false) . $product->getFormattedPrice(false, false);

                        ?></span></td>
                    <?php if ($order->discount > 0): ?>
                    <td><span><?php echo $product->getFormattedCurrency(true, $order->currency, false) . $product->formatAmount($product->discount) ?></span></td>
                    <?php endif; ?>
                    <td><span><?php echo $product->order_quantity ?></span></td>
                    <td style="width:10%"><span><?php

                            if ($product->product_type != 1):

                                if ($order->order_type == 'default') {
                                    echo $product->product_type_text;
                                } else {
                                    echo 'Reference: ' . $order->getOrderInfo()->getData()->reference . '<br><br>';
                                    echo 'Notes: ' . $order->getOrderInfo()->getData()->notes;
                                }



                            else: ?>N/A<?php endif; ?></span></td>
                </tr>



            <?php endforeach; ?>
</tbody>
                </table>

        </div>

    <?php endif; ?>

    <div class="wrap">
        <br><br>
        <a id="contact_toggle" href="#" class="btn btn-primary">Contact Store Owner</a>
        <br><br>


        <form style="display:none;" id="contact_form" class="wrap" action="/shop/myaccount/message/create/" method="post">
            <fieldset class="wrap">
                <legend>Contact <?php

                    $seller_store = $this->getStoreModel()->setData(array(
                        'store_id'  =>  $order->store_id
                    ))->getStoreById();

                    echo $seller_store->getUser(true)->first_name ?></legend>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" class="form-control" placeholder="Subject" name="subject" id="subject" value="Message from: <?php echo $user->first_name ?> - about order ref: <?php echo $order->ref ?>">
                </div>

                <input type="hidden" name="receiver" value="<?php echo $seller_store->user_id ?>">


                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea rows="5" name="message" id="message" placeholder="Email message" class="form-control"></textarea>
                </div>
                <div class="form-group bt">
                    <input type="submit" class="btn btn-primary" value="Send">
                </div>

            </fieldset>


        </form>

    </div>

    <script>
        document.getElementById('contact_toggle').onclick = function() {
            document.getElementById('contact_form').style.display = 'block';
            return false;
        };
    </script>

    <div class="half-left">
        <h3>Order Information</h3>

        <div class="form-group">
            <span>Date</span>
            <span class="form-control"><?php echo $order->getOrderDate(true) ?></span>
        </div>

        <div class="form-group">
            <span>Reference</span>
            <span class="form-control"><?php echo $order->ref ?></span>
        </div>

        <div class="form-group">
            <span>Status</span>
            <span class="form-control"><?php echo $order->status ?></span>
        </div>

        <div class="form-group">
            <span>Total</span>
            <span class="form-control"><?php echo $order->getFormattedTotal(false, $order->currency) ?></span>
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
        <div class="abs-or"></div>

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
                <span class="form-control"><?php echo $payment->getFormattedTotal(true, $order->currency) ?></span>

        <?php endforeach; ?>

    </div>
    <?php endif; ?>

</article>
<script>
    var h1 = document.getElementsByClassName('half-left')[0].clientHeight;
    var h2 = document.getElementsByClassName('half-right')[0].clientHeight;
    if (h1 > h2) {
        document.getElementsByClassName('half-right')[0].style.height = h1 + 'px';
    } else {
        document.getElementsByClassName('half-left')[0].style.height = h2 + 'px';
    }
</script>
<?php include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/footer.php';