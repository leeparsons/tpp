<html><head></head><body>
<table style="width:100%;">
    <tr>
        <td style="text-align: left"><p style="color:#777777;">Hello <?php echo $message->getReceiver(true)->first_name ?>,</p></td>
    </tr>
    <tr>
        <td style="text-align: left"><p style="color:#777777;">You have received a private message on The Photography Parlour from <?php echo $message->getSender(true)->first_name ?></p></td>
    </tr>
    <tr>
        <td style="text-align: left"><p style="color:#777777;"><?php echo $message->getSender(true)->first_name ?> said:</p></td>
    </tr>
    <tr>
        <td style="text-align: left"><p style="color:#777777;"><?php echo $message->getHtmlMessage() ?></p></td>
    </tr>
    <tr>
        <td style="text-align: left"><p style="color:#777777;">You can respond to this message via <a href="<?php echo get_site_url(null, 'shop/myaccount/message/' . $message->message_id . '/') ?>">your account.</p></td>
    </tr>
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