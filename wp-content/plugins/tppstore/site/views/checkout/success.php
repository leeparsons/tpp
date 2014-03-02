<?php get_header(); ?>

<article class="page-article">
    <header>
        <h1>Payment Complete</h1>
    </header>

    <div class="entry-content">
        <p>Thank you <?php echo $user->first_name ?>, for your payment.</p>
        <p>You can view your orders here: <a href="/shop/myaccount/purchases/" class="btn btn-primary">My Purchases</a></p>
        <br>
        <br>
        <p>or <a class="btn btn-primary" href="/shop/">Continue Shopping</a></p>
    </div>
</article>
<script>
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-15329337-1']);
    _gaq.push(['_trackPageview']);
    _gaq.push(['_addTrans',
    '<?php echo $order->ref ?>',           // transaction ID - required
    '<?php echo $store->store_name ?>',  // affiliation or store name
    '<?php echo $order->total; ?>',          // total - required
    '<?php echo $order->tax ?>',           // tax
    '0.00',              // shipping
    '',       // city
    '',     // state or province
    ''             // country
    ]);

    <?php if (count($order_items) > 0): ?>
    <?php foreach ($order_items as $product): ?>
    _gaq.push(['_addItem',
        '<?php echo $order->ref ?>',           // transaction ID - required
        'product-<?php echo $product->product_id ?>',           // SKU/code - required
        '<?php echo esc_textarea($product->product_title)?>',        // product name
        '<?php

         switch ($product->product_type) {
         case '1':
            echo 'Download';
         break;

         case '2':
            echo 'Service';
         break;

         default:
         case '3':
            echo 'Product';
         break;

         case '4':
            echo 'Mentor Session';
         break;

         case '5':
            echo 'Event/Workshop';
         break;


         }

         ?>',   // category or variation
        '<?php echo $product->price - $product->discount ?>',          // unit price - required
        '<?php echo $product->order_quantity ?>'               // quantity - required
    ]);
    <?php endforeach; ?>
    <?php endif; ?>

    _gaq.push(['_set', 'currencyCode', '<?php echo $order->currency ?>']);

    _gaq.push(['_trackTrans']); //submits transaction to the Analytics servers

    (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
</script>
<?php get_footer(); ?>