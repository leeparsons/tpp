<?php

get_header(); ?>

<a href="/shop/">Shop</a> / <span>Mentors</span>

<?php if (count($mentors) > 0): ?>
    <?php $image_size = isset($image_size)?$image_size:'thumb' ?>
<!--    --><?php //echo $paginator->render(); ?>

    <header>
        <h1>Mentors</h1>
        <div class="form-group">
            <label for="sort">Sort by
            <select id="sort">
<!--                <option value="">best rated</option>-->
<!--                <option --><?php //echo $args == 'lowest-rated'?'selected="selected"':'' ?><!-- value="lowest-rated">least rated</option>-->
                <option <?php echo $args == 'a-z'?'selected="selected"':'' ?> value="a-z">Name A-Z</option>
                <option <?php echo $args == 'z-a'?'selected="selected"':'' ?> value="z-a">Name Z-A</option>
            </select>
            </label>

        </div>
    </header>

    <script>
        document.getElementById('sort').onchange = function() {
            window.location.href = '/shop/category/mentors/sort/' + this.value;
        }
    </script>

    </section>

    <section class="wrap wrap-grey">


        <ul class="item-list" id="product_list">
            <?php $i = 1; ?>
            <?php foreach ($mentors as $mentor): ?>
                <li class="item-box<?php echo $i%4?'':' last' ?>">
                    <a href="<?php echo $mentor->getPermalink() ?>">
                        <?php echo $mentor->getSrc($image_size, true) ?>
<?php /*                        <span class="strong"><?php echo $mentor->product_title ?></span> */ ?>

                        <span><?php echo $mentor->mentor_name ?></span>

                        <span><?php echo $mentor->getLocation() ?></span>

                        <?php

                            $specialities = $mentor->getSpecialism()->specialities;

                            if (is_array($specialities) && count($specialities) > 0): ?>
                                <ul class="specialisms">
                                    <?php foreach ($specialities as $speciality): ?>
                                        <li><?php echo $speciality; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        <?php /* <span class="price">From: <?php echo $mentor->getFormattedMinPrice(true) ?></span>*/ ?>
                        <?php /* <a class="store-tag" href="<?php echo $mentor->getStore()->getPermalink(); ?>"><?php echo $mentor->getStore()->store_name ?></a> */ ?>

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