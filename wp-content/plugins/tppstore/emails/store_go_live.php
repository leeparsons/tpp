<html><head><style></style></head><body>
<table style="width:100%;">
    <tr>
        <td><p style="color:#777777;">Hi Rosie!</p></td>
    </tr>
    <tr>
        <td><p style="color:#777777;">The store: <?php echo $store->getSafeTitle() ?> has gone live!</p></td>
    </tr>
    <tr>
        <td>
            <p style="color:#777777;">view this store at: <a href="<?php echo get_site_url(null, $store->getPermalink()) ?>"><?php echo get_site_url(null, $store->getPermalink()) ?></a>.</p>
        </td>
    </tr>
</table>
</body>
</html>