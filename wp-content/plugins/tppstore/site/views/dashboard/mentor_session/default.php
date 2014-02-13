<?php
/**
 * User: leeparsons
 * Date: 05/12/2013
 * Time: 21:04
 */

get_header();
?>

    <article class="page-article-part dashboard">

        <header><h1 class="align-left"><?php echo $product->product_id?'Edit your':'Add a new ' ?> Mentor Session: </h1><strong class="align-left"><?php echo $product->product_title; ?></strong></header>

        <div class="wrap">
            <?php include 'form.php'; ?>
        </div>

    </article>
<?php include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/footer.php';