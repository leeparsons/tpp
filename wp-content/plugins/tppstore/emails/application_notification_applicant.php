<html><head></head><body>
    <table style="width:100%;">
        <tr>
            <td><p style="color:#777777;">Hello <?php echo $user->first_name; ?></p></td>
        </tr>
        <tr>
            <td><p style="color:#777777;">Thank you for your application to sell with us!</p></td>
        </tr>
        <tr>
            <td>
                <p style="color:#777777;">We are reviewing your application and will be in touch shortly. To view your application status please <a style="color:#78A5D4;" href="<?php echo get_site_url(null, 'shop/dashboard') ?>">visit your account</a>.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="color:#777777;">Thanks!</p>
            </td>
        </tr>
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