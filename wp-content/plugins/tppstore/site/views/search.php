<?php

require TPP_STORE_PLUGIN_DIR . 'helpers/paginator.php';

$paginator = new TppStoreHelperPaginator();

$paginator->total_results = $total;

?>
<div class="wrap" id="products">

    <!--<div class="aside-75">-->
    <div class="innerwrap">


        <?php include TPP_STORE_PLUGIN_DIR . 'site/views/products/list.php' ?>

    </div>
</div>