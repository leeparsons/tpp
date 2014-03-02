<?php
/**
 * User: leeparsons
 * Date: 05/12/2013
 * Time: 21:04
 */

get_header();
?>

<article class="page-article-part dashboard">

    <div class="wrap">

        <?php if (false === filter_var($store->paypal_email, FILTER_VALIDATE_EMAIL)): ?>
            <h1 class="align-left"><?php echo $product->product_id?'Edit your':'Add a new ' ?> product: </h1>
            <?php TppStoreMessages::getInstance()->render() ?>
            <pre>You need to complete your payment options on your store before you can start listing products</pre>
        <?php else: ?>


            <div class="aside-25">&nbsp;</div>
                <div class="aside-75">
                    <header><h1 class="align-left"><?php echo $product->product_id?'Edit your':'Add a new ' ?> product: </h1><strong class="align-left"><?php echo $product->product_title; ?></strong></header>
                </div>
            </div>
            <div class="wrap">
                <?php include 'product_form.php'; ?>
            </div>
        <?php endif; //end payment validation ?>
</article>
<?php echo include 'footer.php';