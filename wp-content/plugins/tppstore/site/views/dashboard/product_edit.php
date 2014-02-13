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
        <div class="aside-25">&nbsp;</div>
        <div class="aside-75">
            <header><h1 class="align-left"><?php echo $product->product_id?'Edit your':'Add a new ' ?> product: </h1><strong class="align-left"><?php echo $product->product_title; ?></strong></header>
        </div>
    </div>
    <div class="wrap">
        <?php include 'product_form.php'; ?>
    </div>

</article>
<?php echo include 'footer.php';