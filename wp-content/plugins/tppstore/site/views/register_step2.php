<?php
/**
 * User: leeparsons
 * Date: 01/12/2013
 * Time: 21:44
 */

get_header(); ?>

<script>
    window.location.hash = '';
    console.log(window.location);
</script>

<article class="page-article">

    <header>
        <h1>Store Name</h1>
    </header>

    <?php TppStoreMessages::getInstance()->render(); ?>

    <div class="entry-content">
        <p>To get started with selling your products, enter your store name and slug, your store SEO friendly url, or let us generate one for you.</p>
    </div>

    <form action="/shop/store_register/2" method="post">
        <div class="form-group">
            <label for="store_name">Store Name:</label>
            <input type="text" value="<?php echo $store_model->store_name ?>" class="form-control" id="store_name" name="store_name">
        </div>


        <div class="form-group">
            <label for="store_slug">Store Slug:</label>
            <input type="text" value="<?php echo $store_model->store_slug ?>" class="form-control" id="store_slug" name="store_slug">
        </div>

        <div class="form-group">
            <input type="submit" value="Create Store" class="btn btn-primary">
        </div>

    </form>

    <script>
        document.getElementById('store_name').onkeyup = function() {
            var v = this.value.replace(/[^a-zA-Z0-9-]/g, '-').toLowerCase().replace(/--+/g, '-');

            if (v.substr(v.length-1) == '-') {
                v = v.substr(0, v.length-1);
            }

            if (v.substr(0, 1) == '-') {
                v = v.substr(1);
            }

            document.getElementById('store_slug').value = v;
        }
    </script>
</article>

<?php get_footer();
