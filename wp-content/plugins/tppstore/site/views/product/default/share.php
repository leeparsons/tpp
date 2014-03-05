<?php if (!isset($shared)) $shared = false; ?>
<div class="social-buttons">
    <h3>Share this <?php


        switch ($product->product_type) {
            case '5':
                echo 'Event';
                break;
            case '4':
                echo 'Session';
                break;
            default:
                echo 'Product';
                break;
        }

        ?></h3>
    <div class="align-left">
        <div class="fb-share-button" data-href="<?php echo $product->getPermalink() ?>" data-type="button_count"></div>
    </div>
    <div class="align-left">
        <script type="IN/Share" data-url="<?php echo $product->getPermalink() ?>" data-counter="right"></script>
    </div>

    <div class="align-left">
        <a href="https://twitter.com/share" data-href="<?php echo $product->getPermalink() ?>" class="twitter-share-button">Tweet</a>
    </div>
    <?php if ($shared === false): ?>
        <script src="//platform.linkedin.com/in.js" type="text/javascript">lang: en_US</script>
    <?php endif; ?>
    <div class="align-left">
        <div class="g-plusone" data-href="<?php echo $product->getPermalink() ?>"></div>
    </div>
    <div class="align-left">
        <div class="email-share">Email to a friend</div>
    </div>
</div>
<?php $shared = true;