<html><head></head><body>
<table style="width:100%;">
    <tr>
        <td style="text-align:left;"><p style="color:#777777;">Hello Rosie.</p></td>
    </tr>
    <tr>
        <td style="text-align:left;"><p style="color:#777777;">An order has been placed!</p></td>
    </tr>
    <tr>
        <td style="text-align:left;">
            <p style="color:#777777;"></a>The order reference is: <?php echo $order->ref ?>.</p>
        </td>
    </tr>
</table>

<table style="width:100%">
    <thead>
        <tr>
            <th style="text-align:left;"><p>Order Details</p></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($order_items as $product): ?>
        <tr>
            <td style="text-align: left"><p><?php echo $product->product_title ?></p></td>
            <td style="text-align: left"><p><?php echo $product->getFormattedCurrency(true, $order->currency, false) .  $product->getFormattedPrice(false, false) ?></p></td>
            <td style="text-align: left"><p><?php echo $product->getFormattedCurrency(true, $order->currency, false) . $product->formatAmount($product->discount, false) ?></p></td>
            <td style="text-align: left"><p><?php echo $product->order_quantity ?></p></td>
            <td style="text-align: left"><p><?php echo $product->getFormattedCurrency(true, $order->currency, false) . $product->formatAmount($product->line_total, false, false) ?></p></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<table>
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
        <td style="text-align:left;"><img width="150px" height="96px" src="<?php echo get_site_url(null, 'assets/images/logo-blue-and-pink-email.jpg') ?>" alt="The Photography Parlour"></td>
    </tr>
</table>
</body>
</html>