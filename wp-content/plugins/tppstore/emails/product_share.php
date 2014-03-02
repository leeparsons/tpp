<html><head></head><body>
<table style="width:100%;">
    <tr>
        <td><p style="color:#777777;">Hello, <?php echo $email_from ?> thought you might like to see <?php echo $product->product_title ?> on The Photography Parlour.</p></td>
    </tr>
    <tr>
        <td><p style="color:#777777;">You can view the page here: <a href="<?php echo $product->getPermalink(); ?>"><?php echo $product->getPermalink(); ?></a></p></td>
    </tr>
    </table>
</body>
</html>