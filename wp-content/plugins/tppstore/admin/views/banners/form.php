<div class="wrap">
    <h2><?php

        if (intval($banner->banner_id) > 0) {
            echo 'Editing ' . $banner->title . ' ';
        } else {
            echo 'Add a new ';
        }

        ?>Homepage Banner</h2>

    <br><br>

    <?php TppStoreMessages::getInstance()->renderAdmin(); ?>

    <br><br>
    <form id="banner_form" method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin.php') ?>">

        <input type="hidden" name="banner_id" value="<?php echo $banner->banner_id ?>">

        <input type="hidden" name="banner_ordering" value="<?php echo $banner->ordering ?>">

        <input type="hidden" name="action" value="tpp_save_banner">

        <?php wp_nonce_field('save_banner', 'save_banner') ?>

        <input type="submit" value="Save" class="button-primary">

        <input type="hidden" name="banner_src" value="<?php echo $banner->src ?>">

        <a href="<?php echo admin_url('admin.php?page=tpp-store-banners') ?>" class="button-secondary">Cancel</a>

        <br><br>
        <table class="widefat">
            <tbody>
                <tr>
                    <th>
                        <label for="banner_title">Title (something you would recognise this banner by)</label>
                    </th>
                </tr>
                <tr>
                    <td>
                        <input type="text" id="banner_title" name="banner_title" value="<?php echo $banner->title ?>">
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="banner_link">Link (url where this banners links to)</label>
                    </th>
                </tr>
                <tr>
                    <td>
                        <input type="text" id="banner_link" name="banner_link" value="<?php echo $banner->link ?>">
                    </td>
                </tr>
                <tr>
                    <th>
                        Banner Image:
                    </th>
                </tr>
                <tr>
                    <td>
                        <img src="<?php echo $banner->getSrc() ?>">
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="banner_image">Upload a banner image</label>
                    </th>
                </tr>
                <tr>
                    <td>
                        <?php if (intval($banner->banner_id) > 0): ?>
                        <label for="replace_banner">
                            <input type="checkbox" value="1" id="replace_banner" name="replace_banner">
                            Upload new image</label>
                        <br>
                        <br>
                            <script>
                                document.getElementById('replace_banner').onchange = function() {
                                    if (this.checked === true) {
                                        document.getElementById('banner_image').style.display = 'block';
                                    } else {
                                        document.getElementById('banner_image').style.display = 'none';
                                        document.getElementById('banner_image').value = '';
                                    }
                                };
                            </script>
                        <?php endif; ?>

                        <input <?php echo (intval($banner->banner_id) > 0)?'style="display:none"':'' ?> type="file" id="banner_image" name="banner_image">

                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="banner_enabled">Enabled</label>
                    </th>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" name="banner_enabled" value="1" id="banner_enabled" <?php

                        echo intval($banner->enabled) == 1?'checked="checked"':'';

                        ?>>
                    </td>
                </tr>
            </tbody>
        </table>

        <br><br>

        <input type="submit" value="Save" class="button-primary">

        <a href="<?php echo admin_url('admin.php?page=tpp-store-banners') ?>" class="button-secondary">Cancel</a>

    </form>

</div>



