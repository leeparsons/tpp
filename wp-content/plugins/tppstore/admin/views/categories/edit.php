<div class="wrap">

    <?php TppStoreMessages::getInstance()->render(); ?>

<h3><?php echo $category->category_name ?></h3>

<form enctype="multipart/form-data" method="post" action="<?php echo admin_url('admin.php') ?>">


    <input type="submit" class="button button-primary" value="save">
    <a class="button button-secondary" href="<?php echo admin_url('admin.php?page=tpp-store-categories'); ?>">Cancel</a>
<br><br>
    <input type="hidden" name="category_id" value="<?php echo $category->category_id ?>">
    <?php wp_nonce_field('save_category', 'category_nonce'); ?>
    <input type="hidden" name="action" value="tpp_store_save_ctgy">

    <fieldset>
        <legend>Details</legend>
        <div class="form-group">
            <label for="category_name">Name:</label>
            <input type="text" name="category_name" id="category_name" class="form-control" value="<?php echo $category->category_name ?>">
        </div>


        <div class="form-group">
            <label>Url:</label>
            <input type="text" disabled value="<?php echo $category->getPermalink() ?>" class="form-control-static form-control">
        </div>

        <div class="form-group">
            <label for="category_name">Description:</label>
            <textarea name="category_description" id="category_description" class="form-control"><?php echo $category->description ?></textarea>
        </div>
    </fieldset>


    <fieldset>
        <legend>Published</legend>
        <div class="form-group">

            <label for="enabled_yes">
                <input <?php echo intval($category->enabled) == 1?'checked':'' ?> type="radio" name="enabled" value="1" id="enabled_yes"> Published</label>
            <label for="enabled_no">
                <input <?php echo intval($category->enabled) == 0 || is_null($category->enabled)?'checked':'' ?> type="radio" name="enabled" value="0" id="enabled_no"> Not Published</label>
        </div>
    </fieldset>

    <fieldset>
        <legend>Featured</legend>
        <div class="form-group">

            <label for="featured_yes">
                <input <?php echo intval($category->featured) == 1?'checked':'' ?> type="radio" name="featured" value="1" id="featured_yes"> Featured</label>
            <label for="featured_no">
                <input <?php echo intval($category->featured) == 0 || is_null($category->featured)?'checked':'' ?> type="radio" name="featured" value="0" id="featured_no"> Not featured</label>
        </div>
    </fieldset>


    <fieldset>
        <legend>Image</legend>

        <p>You will need to make sure your store image is 250 pixels by 250 pixels.</p>

        <?php if (false !== ($image = $category->getImageSrc())):  ?>
            <div class="form-group">
                <label>Current Image:</label>
                <br>
                <img src="<?php echo $image ?>">
                <input type="hidden" name="original_image" value="<?php echo $category->src ?>">
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="category_image">Upload New Category Image:</label>
            <input type="file" name="category_image" id="category_image" class="form-control">
        </div>
    </fieldset>


    <input type="submit" class="button button-primary" value="save">
    <a class="button button-secondary" href="<?php echo admin_url('admin.php?page=tpp-store-categories'); ?>">Cancel</a>
</form>
</div>