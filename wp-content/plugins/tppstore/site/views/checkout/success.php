<?php get_header(); ?>

<article class="page-article">
    <header>
        <h1>Payment Complete</h1>
    </header>

    <div class="entry-content">
        <p>Thank you <?php echo $user->first_name ?>, for your payment.</p>
        <p>You can view your orders here: <a href="/shop/myaccount/orders">My Orders</a></p>
    </div>
</article>

<?php get_footer(); ?>