<html><head></head><body>
<table style="width:100%;">
    <tr>
        <td><p style="color:#777777;">Hello <?php echo $store->getUser()->first_name ?>,</p></td>
    </tr>
    <tr>
        <td><p style="color:#777777;">Some of your stock is running out on The Photography Parlour.</p></td>
    </tr>
</table>

<table style="width:100%">
    <thead>
    <tr>
        <th><p>Item</p></th>
        <th><p>Quantity Remaining</p></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($product_quantity_warn as $product): ?>
        <tr>
            <td><p><?php echo $product->product_title ?></p></td>
            <td><p><?php echo $product->quantity_available ?></p></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<table>
    <tr>
        <td style="color:#78A5D4;font-size:12px;font-weight:600">Rosie Parsons</td>
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