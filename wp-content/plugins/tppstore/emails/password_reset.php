<html><head></head><body>
<table style="width:100%;">
    <tr>
        <td><p style="color:#777777;">Hello <?php echo $user->first_name ?>.</p></td>
    </tr>
    <tr>
        <td><p style="color:#777777;">Your password has been reset for The Photography Parlour, your new password is: <?php echo $user->password ?></p></td>
    </tr>
    <tr>
        <td><p style="color:#777777;">We recommend you change this to something memorable through your account.</p></td>
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