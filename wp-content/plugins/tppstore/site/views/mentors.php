<?php

get_header(); ?>

<a href="/shop">Shop</a> / <span>Mentors</span>

<?php if (count($products) > 0): ?>
    <?php $image_size = isset($image_size)?$image_size:'thumb' ?>
<!--    --><?php //echo $paginator->render(); ?>

    <header>
        <h1>Mentors</h1>
        <div class="form-group">
            <label for="sort">Sort by
            <select id="sort">
                <option value="">best rated</option>
                <option <?php echo $args == 'lowest-rated'?'selected="selected"':'' ?> value="lowest-rated">least rated</option>
                <option <?php echo $args == 'a-z'?'selected="selected"':'' ?> value="a-z">A-Z</option>
                <option <?php echo $args == 'z-a'?'selected="selected"':'' ?> value="z-a">Z-A</option>
                <option <?php echo $args == 'highest-price'?'selected="selected"':'' ?> value="highest-price">Price high - low</option>
                <option <?php echo $args == 'lowest-price'?'selected="selected"':'' ?> value="lowest-price">Price low - high</option>
            </select>
            </label>

        </div>
    </header>

    <script>
        document.getElementById('sort').onchange = function() {
            window.location.href = '/shop/mentors/sort/' + this.value;
        }
    </script>

    </section>
    <section class="wrap wrap-grey">



        <ul class="item-list" id="product_list">
            <?php $i = 1; ?>
            <?php foreach ($products as $product): ?>
                <li class="item-box<?php echo $i%4?'':' last' ?>">
                    <a href="<?php echo $product->getPermalink() ?>">
                        <?php echo $product->getProductImage()->getSrc($image_size, true) ?>
                        <span class="strong"><?php echo $product->product_title ?></span>

                        <b class="wrap text-center"><?php echo $product->mentor_name ?></b>
                        <br>
                        <span><?php echo $product->getMentor()->getLocation() ?></span>

                        <?php
/*
                            $specialities = $product->getMentor()->getSpecialism()->specialities;

                            if (is_array($specialities) && count($specialities) > 0): ?>
                                <ul class="specialisms">
                                    <?php foreach ($specialities as $speciality): ?>
                                        <li><?php echo $speciality; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif;

*/
?>
                        <span class="price">From: <?php echo $product->getFormattedMinPrice(true) ?></span>
                        <a class="store-tag" href="<?php echo $product->getStore()->getPermalink(); ?>"><?php echo $product->getStore()->store_name ?></a>
                    </a>
                </li>
                <?php $i++; ?>
            <?php flush();endforeach; ?>
        </ul>
    </section>
    <section class="wrap">
<?php else: ?>
    <header>
        <h1>Mentors</h1>
    </header>
    <p>No Mentors Listed</p>
<?php endif; ?>
<?php get_footer();