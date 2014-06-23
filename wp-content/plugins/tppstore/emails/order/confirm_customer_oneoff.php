<html><head></head><body>
<table style="width:100%;text-align:left;"
<tr>
    <td><p style="color:#777777;">Dear <?php echo $user->first_name; ?></p></td>
</tr>
<tr>
    <td><p style="color:#777777;">Congratulations on your purchase on The Photography Parlour!</p></td>
</tr>
<tr>
    <td><p style="color:#777777;">Item details:</td>
</tr>
<tr>
    <td>
        <p style="color:#777777;"></a>Order id: <?php echo $order->ref ?>.</p>
    </td>
</tr>
</table>

<table style="width:100%;text-align:left;">
    <thead>
    <tr>
        <th style="text-align: left" colspan="2"><p style="color:#777777;">Order Details</p></th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td>

            </td>
        </tr>
        <tr>
            <td style="text-align: left"><p style="color:#777777;">Amount</p></td>
            <td style="text-align: left"><p style="color:#777777;"><?php echo $order->getFormattedCurrency(true, $order->currency, false) .  $order->formatAmount($order->amount, false, false) ?></p></td>
        </tr>
        <tr>
            <td style="text-align: left"><p style="color:#777777;">Payment Reference:</p></td>
            <td style="text-align: left"><p style="color:#777777;"><?php echo $order->getOrderInfo()->reference ?></p></td>
        </tr>
        <tr>
            <td style="text-align: left"><p style="color:#777777;">Payment Notes:</p></td>
            <td style="text-align: left"><p style="color:#777777;"><?php echo $order->getOrderInfo()->notes ?></p></td>
        </tr>
    </tbody>
</table>


<table style="width:100%;text-align:left;"
<tbody>
<tr>
    <td><p style="color:#777777;">You can view all your purchases in <a href="<?php echo get_site_url(null, 'shop/myaccount/purchases/') ?>">your account</a>.</p></td>
</tr>
<tr>
    <td><p style="color:#777777;">We appreciate all feedback - if you have any thoughts, questions or feedback on services provided on the site, or the site design itself please do get in touch with us at: chitchat@thephotographyparlour.com</p></td>
</tr>
<tr>
    <td><p style="color:#777777;">Thanks again for your continued support of The Photography Parlour - we appreciate it! Please keep checking back for new photography products, workshops and mentor sessions and if you know someone who might like our site please share the link!</p></td>
</tr>
</tbody>
</table>


<table style="width:100%;text-align:left;"
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