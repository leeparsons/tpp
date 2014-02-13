<html><head><style></style></head><body>
<table style="width:100%;">
    <tr>
        <td><p style="color:#777777;">Hi Rosie!</p></td>
    </tr>
    <tr>
        <td><p style="color:#777777;">You have received an application from <?php echo $user->getName(); ?> to sell with The Photography Parlour!</p></td>
    </tr>
    <tr>
        <td>
            <p style="color:#777777;">They have been notified that you are reviewing their application, the details of which are below.</p>
        </td>
    </tr>
    <tr>
        <td>
            <p style="color:#777777;">You can review the application here: <a href="<?php echo admin_url('admin.php?page=tpp-store-approvals') ?>" style="color:#78a5d4"><?php echo admin_url('index.php?pagename=tpp-store-approvals') ?></a></p>
        </td>
    </tr>
</table>

<table>
    <tr>
        <td colspan="2" style="color:#78A5D4">Business Details</td>
    </tr>
    <tr>
        <td style="color:#777777">Business Name:</td>
        <td style="color:#78A5D4"><?php echo $store->store_name ?></td>
    </tr>
    <tr>
        <td style="color:#777777">URL:</td>
        <td style="color:#78A5D4"><?php echo $store->url ?></td>
    </tr>
    <tr>
        <td style="color:#777777">Description:</td>
        <td style="color:#78A5D4"><pre><?php echo esc_textarea($store->description) ?></pre></td>
    </tr>
    <tr>
        <td style="color:#777777">Country:</td>
        <td style="color:#78A5D4"><?php echo $store->country ?></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" style="color:#78A5D4">User Details</td>
    </tr>
    <tr>
        <td style="color:#777777">Name:</td>
        <td style="color:#78A5D4"><?php echo $user->getName() ?></td>
    </tr>
    <tr>
        <td style="color:#777777">Email:</td>
        <td style="color:#78A5D4;"><?php echo $user->email ?></td>
    </tr>
</table>

<table>
    <tr>
        <td>They have <?php if ($store->newsletter != 1) {echo 'not ';} ?>signed up for receiving newsletters.</td>
    </tr>
    <tr>
        <td>They heard about you: <?php echo $store->how ?>.</td>
    </tr>
</table>


</body>
</html>