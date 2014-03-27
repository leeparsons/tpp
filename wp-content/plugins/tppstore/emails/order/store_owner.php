<table>
    <tbody>
    <tr>
        <td colspan="4" style="text-align: left"><p style="color:#777777;">Seller Details:</p></td>
    </tr>

    <tr>
        <td colspan="4" style="text-align: left"><p style="color:#777777;"><?php

                $store_user = $store->getUser();

                echo $store->getOwner() . '<br>';

                echo $store->store_name . '<br>';

                echo '<a href="' . $store->getPermalink(false, true) . '">' . $store->getPermalink(false, true) . '</a><br>';

                if (trim($store_user->address) != '') {
                    echo nl2br($store_user->address) . '<br>';
                }



                ?></p></td>
    </tr>

    <tr>
        <td colspan="4" style="text-align: left"><p style="color:#777777;">If you have any questions you can send the store owner an internal message from your <a href="<?php echo get_site_url(null, 'shop/myaccount/purchase/' . $order->order_id) ?>">purchase details page</a>.</p></td>
    </tr>

    </tbody>
</table>