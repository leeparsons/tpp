<?php get_template_part('common/head'); ?>
<?php if (!wp_is_mobile() || tpp_is_tablet()): ?>
    <?php get_template_part('menus/main_menu') ?>
<div class="wrap">
    <section id="header_category" class="header-slideshow">

    </section>
</div>
<?php endif; ?>
<div class="wrap">
    <section class="innerwrap"><?php flush();