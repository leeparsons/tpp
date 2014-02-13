<?php

ob_start();
?>

<!DOCTYPE html>
<html lang="en" style="margin:0;text-align:center;padding:0;">
    <head>
        <title>The Photography Parlour</title>


        

    </head>


<body style="margin:0;text-align:center;padding:0;background-color: #e8e8e8;">

<div style="width:800px;margin:auto;display:block;background-color: #FFFFFF;padding:20px;text-align: left">
        <h1>Thank you for your order</h1>

        <p style="display:block;width:100%;margin-bottom:10px;">Hi Rosie,</p>

        <p style="display:block;width:100%;margin-bottom:10px;">Your order has been received. Details of your order are listed below:</p>
        <br>
        <p style="display:block;width:100%;margin-bottom:10px;"><strong>Order ID</strong>: (for your reference - please quote this if you need to contact us about your order) <span style="color: #5b5b5b;">abd454re46745</span></p>
        <p style="display:block;width:100%;margin-bottom:10px;"><strong>Order Date:</strong> <span style="color: #5b5b5b;">12th March 2013</span></p>
        <p style="display:block;width:100%;margin-bottom:10px;"><strong>Order Items:</strong></p>

        <table style="width:100%;text-align:left"  cell-spacing="0" cell-padding="0" border-collapse="collapse">
            <thead>
                <tr>
                    <th style="border-bottom:1px solid #e7e7e7;padding-bottom:5px;">Title</th>
                    <th style="border-bottom:1px solid #e7e7e7;padding-bottom:5px;text-align:right">Quantity</th>
                    <th style="border-bottom:1px solid #e7e7e7;padding-bottom:5px;text-align:right">Amount</th>
                    <th style="border-bottom:1px solid #e7e7e7;padding-bottom:5px;text-align:right">Tax</th>
                    <th style="border-bottom:1px solid #e7e7e7;padding-bottom:5px;text-align:right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>First Product</td>
                    <td style="text-align:right">2</td>
                    <td style="text-align:right">&pound;10.00</td>
                    <td style="text-align:right">&pound;2.00</td>
                    <td style="text-align:right">&pound;24.00</td>
                </tr>
                <tr class="last">
                    <td style="border-bottom:1px solid #e7e7e7;padding-bottom:5px;">Second Product</td>
                    <td style="border-bottom:1px solid #e7e7e7;padding-bottom:5px;text-align:right">1</td>
                    <td style="border-bottom:1px solid #e7e7e7;padding-bottom:5px;text-align:right">&pound;30.00</td>
                    <td style="border-bottom:1px solid #e7e7e7;padding-bottom:5px;text-align:right">&pound;6.00</td>
                    <td style="border-bottom:1px solid #e7e7e7;padding-bottom:5px;text-align:right">&pound;31.50</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top:10px;text-align:right"><strong>Total:</strong></td>
                    <td style="padding-top:10px;text-align:right">&pound;50.00</td>
                    <td style="padding-top:10px;text-align:right">&pound;10.00</td>
                    <td style="padding-top:10px;text-align:right">&pound;55.50</td>
                </tr>
            </tfoot>
        </table>

    <p style="display:block;width:100%;margin-bottom:10px;"><strong>Additional Details &amp; Information</strong>:</p>

    <p style="display:block;width:100%;margin-bottom:10px;">To access your download, click here. Once you have clicked on the slipper button, download the monix and eat the tasty nosh.</p>

    <p style="display:block;width:100%;margin-bottom:10px;"><strong>Accessing Your Order History</strong> <span class="staic-text">You can access your order history by <a style="color:#9bccfa" href="http://wwww.thephotogrphyparlour.com/shop/store_login">logging into your account on The Photography Parlour</a></p>


    <p style="display:block;width:100%;margin-bottom:10px;">If you have any questions about this order, you may find our <a style="color:#9bccfa" href="shop-faqs">FAQs section helpful</a></p>

    <p style="display:block;width:100%;margin-bottom:10px;"><a style="color:#9bccfa" href="http://www.thephotographyparlour.com">&copy; The Photography Parlour 2014</a></p>

</div>

</body>
</html>

<?php $contents = ob_get_contents();




ob_end_clean();

$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=iso-8859-1\r\n";
$headers .= "To:rosie@rosieparsons.com\r\n";
$headers .= "From: Chit Chat <chitchat@thephotographyparlour.com>\r\n";
$headers .= "Reply-To:Chit Chat <chitchat@thephotographyparlour.com>\r\n";
$headers .= "X-Priority: 1\r\n";

mail('rosieannparsons@gmail.com', 'Your order has been received', $contents, $headers, "-f chitchat@thephotographyparlour.com");

?>sent!