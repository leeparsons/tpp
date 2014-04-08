<div class="wrap">
    <h2>Homepage Banners <a href="<?php echo admin_url('admin.php?page=tpp-store-banner') ?>" class="add-new-h2">Add New</a></h2>


<form id="ordering_form">
    <table class="wp-list-table widefat fixed posts sortable" id="banners_table">
        <thead>
            <tr>
                <th></th>
                <th>Title</th>
                <th>Link</th>
                <th>Banner</th>
                <th>Enabled</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($banners) > 0): ?>
            <?php foreach ($banners as $banner): ?>
                <?php $edit_url = admin_url('admin.php?page=tpp-store-banner&id=' . $banner->banner_id) ?>
            <tr>
                <td class="move">
                    <input type="hidden" class="ordering" name="ordering[]" value="<?php echo $banner->banner_id ?>">
                    <img src="<?= TPP_STORE_PLUGIN_URL . '/assets/images/icon_move.png' ?>">
                </td>
                <td><a href="<?php echo $edit_url ?>"><?php echo $banner->title; ?></a></td>
                <td><a href="<?php echo $banner->link; ?>" target="_blank"><?php echo $banner->link ?></a></td>
                <td><a href="<?php echo $edit_url ?>"><img src="<?php echo $banner->getSrc(); ?>" width="200" height="auto"></a></td>
                <td><a href="<?php echo $edit_url ?>">
                        <img src="/assets/images/<?php echo $banner->enabled == 1?'tick.png':'cross.png' ?>">
                    </a></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No banners</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</form>
</div>

<script>

    jQuery(function ( $ ) {

        $('table.sortable tbody').sortable({
            handle: '.move img',
            placeholder: 'sortable-placeholder',
            forcePlaceholderSize: true,
            update: function ( e, ui ) {

                var data = [];

                if ($('.ordering').length > 0) {

                    $('.ordering').each(function() {
                        data.push($(this).val());
                    });
                }


                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'tpp_save_banner_ordering',
                        ordering: data.join(':')
                    }
                });

            }
        });

    });

</script>