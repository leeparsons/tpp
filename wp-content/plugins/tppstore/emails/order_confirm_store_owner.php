<html><head></head><body>
<table style="width:100%;">
    <tr>
        <td style="text-align: left"><p style="color:#777777;">Dear <?php echo $store->getUser()->first_name; ?></p></td>
    </tr>
    <tr>
        <td style="text-align: left"><p style="color:#777777;">Congratulations, you've made a sale on The Photography Parlour!</p></td>
    </tr>
</table>

<table style="width:100%">
    <thead>
    <tr>
        <th style="text-align: left" colspan="6">
            <p style="color:#777777;">The order reference is: <a href="<?php echo get_site_url(null, '/shop/dashboard/order/' . $order->ref . '/') ?>"><?php echo $order->ref; ?></a>.</p>
        </th>
    </tr>
        <tr>
            <th colspan="6" style="text-align: left"><p style="color:#777777;">Order Details</p></th>
        </tr>
        <tr>
            <th style="text-align: left"><p style="color:#777777;">Item</p></th>
            <th style="text-align: left"><p style="color:#777777;">Price</p></th>
            <th style="text-align: left"><p style="color:#777777;">Quantity</p></th>
            <th style="text-align: left"><p style="color:#777777;">Discount</p></th>
            <th style="text-align: left"><p style="color:#777777;">Tax</p></th>
            <th style="text-align: left"><p style="color:#777777;">Line Total</p></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($order_items as $product):

            $cur = $product->getFormattedCurrency(true, $order->currency, false);

            ?>
        <tr>
            <th style="text-align: left"><p style="color:#777777;"><a href="<?php echo $product->getPermalink(true) ?>"><?php echo $product->product_title ?></a></p></th>
            <th style="text-align: left"><p style="color:#777777;"><?php echo $cur .  $product->getFormattedPrice(false, false) ?></p></th>
            <th style="text-align: left"><p style="color:#777777;"><?php echo $product->order_quantity ?></p></th>
            <th style="text-align: left"><p style="color:#777777;"><?php echo $cur . $product->formatAmount($product->discount, false) ?></p></th>
            <th style="text-align: left"><p style="color:#777777;"><?php echo $cur . $product->getFormattedTax() ?></p></th>
            <th style="text-align: left"><p style="color:#777777;"><?php echo $cur . $product->formatAmount($product->line_total, false, false) ?></p></th>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td style="text-align: right;"><p style="color:#777777">Total Tax: <?php echo $cur . $order->tax ?></p></td>
        </tr>
        <tr>
        <td style="text-align: right;"><p style="color:#777777">Total Amount: <?php echo $cur . $order->total ?></p></td>
    </tr>
    </tbody>
</table>



<table>
    <thead>
    <tr>
        <th colspan="2" style="text-align: left"><p style="color:#777777;">Customer Details:</p></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="text-align: left"><p style="color:#777777;">Name:</p></td>
        <td style="text-align: left"><p style="color:#777777;"><?php echo $user->first_name ?></p></td>
    </tr>
    <?php if (trim($user->address) != ''): ?>
    <tr>
        <td style="text-align: left"><p style="color:#777777;">Address:</p></td>
        <td><p style="color:#777777;"><?php echo nl2br($user->address) ?></p></td>
    </tr>
    <?php endif; ?>
    <tr>
        <td style="text-align: left"><p style="color:#777777;">Email:</p></td>
        <td style="text-align: left"><p style="color:#777777;"><?php echo $user->email; ?><p></p></td>
    </tr>
    </tbody>
</table>


<table>
    <tbody>
    <tr>
        <td style="text-align:left;"><p style="color:#777777;">Please contact the buyer asap to touch base if you have sold a workshop space, mentor session or other service. You do not need to contact the buyer if you have sold an instant download. You can send them an email from <a href="<?php echo get_site_url(null, 'shop/dashboard/order/' . $order->ref . '/') ?>">your dashboard</a></p></td>
    </tr>
    <tr>
        <td style="text-align: left"><p style="color:#777777">These details will be saved on <a href="<?php echo get_site_url(null, '/shop/dashboard/') ?>">your dashboard</a>.</p></td>
    </tr>
    </tbody>
</table>

<table>
    <tr>
        <td style="text-align:left;color:#78A5D4;font-size:12px;font-weight:600">Thanks again for your continued support of The Photography Parlour!</td>
    </tr>
    <tr>
        <td style="text-align:left;color:#78A5D4;font-size:12px;font-weight:600">All the best!</td>
    </tr>
    <tr>
        <td style="text-align:left;color:#78A5D4;font-size:12px;font-weight:600">Rosie Parsons</td>
    </tr>
    <tr>
        <td style="text-align:left;color:#78A5D4;font-size:12px;">The Photography Parlour</td>
    </tr>
    <tr>
        <td style="text-align:left;color:#777777;font-size:10px;">E: rosie@thephotographyparlour.com</td>
    </tr>
    <tr>
        <td style="text-align:left;"><img width="150px" height="96px" src="<?php echo get_site_url(null, '/assets/images/logo-blue-and-pink-email.jpg') ?>" alt="The Photography Parlour"></td>
    </tr>
</table>
</body>
</html>