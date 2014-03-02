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
<?php

$mentor_session = false;
$event = false;

?>

<table style="width:100%;text-align:left;">
    <thead>
    <tr>
        <th style="text-align: left" colspan="4"><p style="color:#777777;">Order Details</p></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($order_items as $product):

        if ($product->product_type == 4) {
            $mentor_session = true;
        } elseif ($product->product_type == 5) {
            $event = true;
        }

        ?>
        <tr>
            <td colspan="4" style="text-align: left"><p style="color:#777777;"><a style="color:#78A5D4" href="<?php echo $product->getPermalink() ?>"><?php echo $product->product_title ?></a></p></td>
        </tr>
        <tr>
                <td style="text-align: left"><p style="color:#777777;">Item price</p></td>


                <td style="text-align: left"><p style="color:#777777;"><?php echo $product->discount > 0?'Discount':'&nbsp;'; ?></p></td>


                <td style="text-align: left"><p style="color:#777777;">Quantity</p></td>


                <td style="text-align: left"><p style="color:#777777;">Line total</p></td>

        </tr>
        <tr>
            <td style="text-align: left"><p style="color:#777777;"><?php echo $product->getFormattedCurrency(true, $order->currency, false) .  $product->getFormattedPrice(false, false) ?></p></td>
            <td style="text-align: left"><p style="color:#777777;"><?php echo $product->discount > 0?$product->getFormattedCurrency(true, $order->currency, false) . $product->formatAmount($product->discount, false, false):'&nbsp;' ?></p></td>
            <td style="text-align: left"><p style="color:#777777;"><?php echo $product->order_quantity ?></p></td>
            <td style="text-align: left"><p style="color:#777777;"><?php echo $product->getFormattedCurrency(true, $order->currency, false) . $product->formatAmount($product->line_total, false, false) ?></p></td>
        </tr>
        <?php if ($product->product_type == 5): //event ?>

        <tr>
            <td colspan="4" style="text-align: left"><p style="color:#777777;">Event Details:</p></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: left"><p style="color:#777777;">Location: <?php $event = new TppStoreModelEvent();


                    $event->setData(array(
                        'product_id'    =>  $product->product_id
                    ))->getProductById();

                    $event->loadEventData();

                    echo nl2br($event->address);

                    ?></p></td>
            <td colspan="2" style="text-align: left"><p style="color:#777777;">Dates: <?php echo $event->getEventDisplayDateRange() ?></p></td>
        </tr>

        <?php elseif ($product->product_type == 4): //mentor session ?>
<?php /*
            <tr>

                <td colspan="4" style="text-align:left;"><p style="color:#777777;">Mentor: <?php $mentor = new TppStoreModelMentor();

                    $mentor->getMentorByProduct(false, $product->product_id);

                    echo $mentor->mentor_name;

                    ?></p></td>
            </tr>
*/ ?>
        <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
</table>

<table style="width:100%;text-align:left;">
    <thead>
    <tr>
        <th style="text-align: left">Store: <?php echo $store->store_name ?></th>
    </tr>
    </thead>
    <tr>
        <td style="text-align: left"><p style="color:#777777;">If you have questions regarding this purchase, please contact the store owner from their <a href="<?php echo $store->getPermalink(); ?>" store page</a></p></td>
    </tr>
</table>

<table style="width:100%;text-align:left;"
<tbody>
    <?php if ($mentor_session === true): ?>
        <tr>
            <td><p style="color:#777777;">Your mentor should be touching base with you via email shortly. If you do not hear from them within 48 hours please feel free to contact the <a href="<?php echo $store->getPermalink() ?>">store owner.</a></p></td>
        </tr>
    <?php endif; ?>
    <tr>
        <td><p style="color:#777777;">You can view all your purchases in <a href="<?php echo get_site_url(null, 'shop/myaccount/') ?>">your account</a>.</p></td>
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