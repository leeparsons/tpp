<div class="wrap">

    <h1>Product: <?php echo $product->product_title ?></h1>

    <table class="wp-admin-list">
        <tbody>
            <tr>
                <td>Type</td>
                <td><?php echo $product->getProductType(); ?></td>
                <?php if ($product->product_type == 1):  ?>
                <tr>
                    <td>Download link:</td>
                    <td><?php echo $product->product_type_text ?></td>
                </tr>
                <tr>
                    <td>Download File:</td>
                    <td><a href="<?php echo $product->getAdminDownloadUrl() ?>">click me!</a></td>
                </tr>
                <?php else: ?>
                <tr>
                    <td>Extra Information:</td>
                    <td><?php echo $product->product_type_text ?></td>
                </tr>
                <?php endif; ?>
        </tbody>
    </table>
</div>