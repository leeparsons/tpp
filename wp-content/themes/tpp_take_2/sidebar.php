<section class="aside-25 sidebar page-aside">

    <?php TppStoreControllerProduct::getInstance()->renderLatestProductsSideBar(); ?>

    <div class="widget">
        <h4>Blog Categories</h4>
        <ul class="blog-categories">
        <?php wp_list_categories(array(
            'title_li'  =>  ''
        )) ?>
        </ul>
    </div>

</section>