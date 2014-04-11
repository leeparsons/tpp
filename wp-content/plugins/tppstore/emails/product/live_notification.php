<html><head><style></style></head><body>
<table style="width:100%;">
    <tr>
        <td><p style="color:#777777;">Hi Rosie!</p></td>
    </tr>
    <tr>
        <td><p style="color:#777777;"><?php echo $store->getOwner() ?> has put another <?php echo $product->getProductType() ?> live today.</p></td>
    </tr>
    <tr>
        <td>
            <p style="color:#777777;">You can see the <?php echo $product->getProductType() ?> here: <a href="<?php echo $product->getPermalink() ?>"><?php echo $product->getPermalink() ?></a></p>
        </td>
    </tr>
</table>

</body>
</html>