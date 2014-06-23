<?php

query_posts(array(
    'posts_per_page'    =>  5
));

if (have_posts()):

    $i = 0; ?>
    <div class="post-slides">
        <?php while (have_posts()): the_post(); ?>
            <?php $i++; ?>
            <article class="post-slide" <?php echo $i > 1?'style="display:none"':'' ?>>
                <a href="<?php the_permalink() ?>"><?php the_post_thumbnail('blog_post_slide'); ?></a>
                <div class="post-info">
                    <header><h3><a href="<?php the_permalink() ?>"><?php echo tpp_limit_content(get_the_title(), 60, ''); ?></a></h3></header>
                    <div class="hentry excerpt">
                        <p><a href="<?php the_permalink() ?>"><?php echo tpp_limit_content(get_the_excerpt(), 120) ?></a></p>
                        <a class="readmore" href="<?php the_permalink() ?>">Read More</a>
                    </div>
                </div>
            </article>
            <?php flush(); endwhile; ?>
        <nav id="slide_navigation">
            <?php for ($x = 0; $x < $i; $x++): ?>
                <a data-index="<?php echo $x; ?>" href="#" class="navi <?php echo $x == 0 ? 'active':'' ?>"></a>
            <?php endfor; ?>
        </nav>
    </div>
    <!--        <div class="wrap navigation">-->
    <!--            --><?php //posts_nav_link(' ', '<span class="align-right btn btn-primary">Recent Posts</span>', '<span class="align-left btn btn-primary">Previous Posts</span>'); ?>
    <!--        </div>-->

<?php endif; ?>
<script>

    var postSlider = {
        slides:{},
        navis: {},
        it: 0,
        to: 0,
        init: function() {

            this.slides = document.getElementsByClassName('post-slide');
            this.navis = document.getElementsByClassName('navi');

            document.getElementById('slide_navigation').style.display = 'block';

            this.setUpSlides();
            this.setUpNavis();


            this.startSlides();

        },
        setUpSlides: function() {
            for (var x = 0; x < this.slides.length; x++) {

                if (x > 0) {
                    this.slides[x].setAttribute('data-active', false);
                    this.slides[x].style.opacity = 0;
                    this.slides[x].style.display = 'block';
                } else {
                    this.slides[x].setAttribute('data-active', true);
                }
            }
        },
        setUpNavis: function() {
            for (var x = 0; x < this.navis.length; x++) {
                this.navis[x].onclick = function() {
                    return postSlider.rotateToIndex(this.getAttribute('data-index'));
                }
            }
        },
        startSlides: function() {
            postSlider.to = setTimeout(function() {
                postSlider.it = setInterval(function() {

                    for (var x = 0; x < postSlider.slides.length; x++) {

                        if (postSlider.slides[x].getAttribute('data-active') == 'true') {

                            postSlider.rotateSlides(x);
                            break;
                        }
                    }

                }, 4000);
            }, 5000);
        },
        rotateSlides: function(x) {
            var y = 0;

            x = parseInt(x);

            if (x < postSlider.slides.length  - 1) {
                y = x+1;
            }

            var active_index = postSlider.getActiveIndex();

            postSlider.hideSlide(active_index);

            postSlider.showSlide(y);

            postSlider.hideNavi(active_index);
            postSlider.showNavi(y);
        },
        hideSlide: function(x) {
            postSlider.slides[x].style.opacity = 0;
            postSlider.slides[x].setAttribute('data-active', false);
        },
        showSlide: function(y) {
            postSlider.slides[y].style.opacity = 1;
            postSlider.slides[y].setAttribute('data-active', true);
        },
        hideNavi: function(x) {
            postSlider.navis[x].setAttribute('class', 'navi');
        },
        showNavi: function(x) {
            postSlider.navis[x].setAttribute('class', 'navi active');
        },
        rotateToIndex: function(x) {

            clearTimeout(postSlider.to);
            clearInterval(postSlider.it);

            active_index = postSlider.getActiveIndex();

            console.log(active_index);

            postSlider.hideNavi(active_index);
            postSlider.hideSlide(active_index);

            postSlider.showSlide(x);
            postSlider.showNavi(x);
            postSlider.startSlides();

            return false;
        },
        getActiveIndex: function() {
            for ( var i = 0; i < postSlider.slides.length; i++) {
                if (postSlider.slides[i].getAttribute('data-active') == 'true') {
                    return i;
                }
            }
            return 0;

        }

    };

    postSlider.init();

</script>