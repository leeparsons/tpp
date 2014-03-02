<?php
/**
 * User: leeparsons
 * Date: 05/12/2013
 * Time: 19:26
 */

//wp_enqueue_script('mentor-list', TPP_STORE_PLUGIN_URL . '/site/assets/js/dashboard/mentors-ck.js', array(), 1, true);

include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/header.php';

if ($mentor_count == 0) {
    TppStoreMessages::getInstance()->addMessage('error', 'You have no mentors listed - create a mentor to start listing mentor sessions!');
}

?>
<article class="page-article-part">

    <header><h1>mentors</h1></header>

    <?php TppStoreMessages::getInstance()->render(); ?>

    <?php if (false === filter_var($store->paypal_email, FILTER_VALIDATE_EMAIL)): ?>

    <pre>You need to complete your payment options on your store before you can start listing products</pre>
    <?php else: ?>


        <div class="wrap">
            <a class="btn btn-primary" href="/shop/dashboard/mentor/new">Add a mentor</a>
            <br><br>
        </div>



        <?php if ($mentor_count > 0): ?>

            <div class="wrap">
                <div class="align-right">
            <?php

            include TPP_STORE_PLUGIN_DIR . 'helpers/paginator.php';

            $paginator = new TppStoreHelperPaginator();

            $paginator->total_results = $mentor_count;

            echo $paginator->render();

            ?></div>
            </div>
            <ul class="item-list">
                <?php foreach ($mentors as $mentor): ?>
                    <?php $edit_url = "/shop/dashboard/mentor/edit/" . $mentor->mentor_id; ?>
                    <li class="item-box">
                        <a href="<?php echo $edit_url ?>"><?php echo $mentor->getSrc('cart_thumb', true); ?></a>
                        <a href="<?php echo $edit_url ?>"><strong><?php echo $mentor->mentor_name ?></strong></a>
                        <div class="dropdown">
                            <a class="dropdown-option btn-go" href="/shop/dashboard/mentor_session/new/<?php echo $mentor->mentor_id ?>">Add a Session</a>
                            <a class="dropdown-option" href="<?php echo $edit_url ?>">Edit mentor</a>
                            <a class="dropdown-option" href="/shop/dashboard/mentor-sessions/<?php echo $mentor->mentor_id ?>">Edit Sessions (<?php echo $mentor->getSessionCount() ?>)</a>
                            <form method="post" action="/shop/dashboard/mentor_delete/">
                                <input type="hidden" name="m" value="<?php echo $mentor->mentor_id ?>">
                                <input type="submit" class="dropdown-option btn-danger" value="Delete">
                            </form>

                        </div>
                    </li>

                <?php endforeach; ?>
            </ul>

        <?php endif; ?>
    <?php endif; //end payment checks ?>
</article>
<script>
    jQuery(function($) {
        $('.item-box').on('submit', 'form', function(e) {
            var c = confirm('Are you sure you wish to delete this mentor and all their sessions?');
            if (c === false) {
                e.preventDefault();
            }
        });
    });
</script>

<?php include TPP_STORE_PLUGIN_DIR . 'site/views/dashboard/footer.php';
