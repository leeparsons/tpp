<?php

function TppRenderTds($store)
{
    ?><td><a href="<?php echo admin_url('admin.php?page=tpp-store-application&sid=' . $store->store_id) ?>"><?php echo $store->store_name; ?></a></td>
    <td><?php echo date('jS F, Y', strtotime($store->created_on)); ?></td>
    <td style="text-align:center;"><img src="/assets/images/<?php


        if ($store->approved == 1) {
            echo 'tick.png';
        } elseif ($store->approved == 0) {
            echo 'cross.png';
        } else {
            echo 'declined.png';
        }


        ?>"></td>
    <td><?php echo $store->owner; ?></td>
    <td><?php echo $store->product_count; ?></td>
<?php
}

?>
<div class="wrap">

    <table class="wp-list-table widefat fixed posts">
        <thead>
        <tr>
            <th>Title</th>
            <th>Application Date</th>
            <th style="text-align: center">Approved</th>
            <th>Owner</th>
            <th>Number of Products</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($applications) > 0): ?>
        <?php foreach ($applications as $store): ?>
            <tr>
                <?php TppRenderTds($store); ?>
            </tr>
        <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No Applications</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>