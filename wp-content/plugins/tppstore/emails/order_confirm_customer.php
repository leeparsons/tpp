<html><head></head><body>
<table style="width:100%;">
    <tr>
        <td><p style="color:#777777;">Hello <?php echo $user->first_name; ?></p></td>
    </tr>
    <tr>
        <td><p style="color:#777777;">Your order was placed successfully!</p></td>
    </tr>
    <tr>
        <td>
            <p style="color:#777777;"></a>Your order reference is: <?php echo $order->ref ?>.</p>
        </td>
    </tr>
</table>

<table style="width:100%">
    <thead>
        <tr>
            <th><p>Order Details</p></th>
        </tr>
        <tr>
            <th><p>Title</p></th>
            <th><p>Price</p></th>
            <th><p>Quantity</p></th>
            <th><p>Line Total</p></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($order_items as $product): ?>
        <tr>
            <th><p><?php echo $product->product_title ?>></p></th>
            <th><p><?php echo $product->getFormattedPrice(true) ?>></p></th>
            <th><p><?php echo $product->order_quantity ?>></p></th>
            <th><p><?php echo $product->getLineItemFormattedTotal(true) ?>></p></th>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<table>
    <tr>
    <tr style="color:#78A5D4;font-size:12px;font-weight:600">Rosie Parsons</td>
    </tr>
    <tr>
        <td style="color:#78A5D4;font-size:12px;">The Photography Parlour</td>
    </tr>
    <tr>
        <td style="color:#777777;font-size:10px;">E: rosie@thephotographyparlour.com</td>
    </tr>
    <tr>
        <td><img width="150px" height="96px" src="<?php echo get_site_url(null, 'assets/images/logo-blue-and-pink-email.jpg') ?>" alt="The Photography Parlour"></td>
    </tr>
</table>
</body>
</html>