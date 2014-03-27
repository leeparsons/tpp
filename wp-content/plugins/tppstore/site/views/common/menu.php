<nav class="menu-btns">
<!--    --><?php //if (tpp_is_blog() || tpp_is_on_blog_page()): ?>
        <a href="/shop" class="btn sell btn-primary">Shop</a>
<!--    --><?php //else: ?>
        <a href="/blog" class="blog btn btn-primary">Blog</a>
<!--    --><?php //endif; ?>
    <?php if (false === $user): ?>
<!--        <a href="/shop/store_register" class="signup-btn">Sign Up</a>-->
        <a href="/shop/store_login" class="login-btn btn-primary btn">Login</a>
        <span class="or">or</span>
        <a href="#" class="btn-facebook">Login with Facebook</a>
        <a href="/shop/cart/" class="basket-btn">My Basket: <?php echo TppStoreControllerCart::getCartTotalsFormatted() ?></a>
        <a href="/shop/sell-with-us" class="btn btn-primary sell">Sell with us</a>
    <?php else: ?>
        <a href="/shop/<?php echo $user->user_type == 'store_owner'?'dashboard':'myaccount' ?>"><?php echo $user->user_type == 'store_owner'?'My Dashboard':'My Account' ?></a>
        <a href="/shop/cart/" class="basket-btn">My Basket: <?php echo TppStoreControllerCart::getCartTotalsFormatted() ?></a>
        <a href="/shop/store_logout" class="btn btn-danger">Logout</a>
        <?php if($user->user_type != 'store_owner'): ?>
            <a href="/shop/sell-with-us" class="btn btn-primary sell">Sell with us</a>
        <?php else: ?>
            <a href="/shop/dashboard/product_add" class="btn btn-primary sell">List a Product</a>
        <?php endif; ?>
    <?php endif; ?>
</nav>
