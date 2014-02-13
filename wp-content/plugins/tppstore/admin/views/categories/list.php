<?php

    function TppRenderTds($category)
    {
?><td><a href="<?php echo admin_url('admin.php?page=tpp-store-category&id=' . $category['category_id']) ?>"><?php echo $category['category_name']; ?></a></td>
<td><?php echo $category['featured']; ?></td>
<td><?php echo $category['product_count'] ?></td><?php
    }

?>
<div class="wrap">
<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Featured</th>
            <th>Products in category</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($categories->categories as $category): ?>
    <tr>
        <?php TppRenderTds($category); ?>
    </tr>
        <?php if( count($category['children']) > 0): ?>
            <?php foreach ($category['children'] as $child): ?>
            <tr class="child child-level-1">
                <?php TppRenderTds($child) ?>
            </tr>
                <?php if( count($child['children']) > 0): ?>
                    <?php foreach ($child['children'] as $grand_child): ?>
                        <tr class="child child-level-2">
                            <?php TppRenderTds($grand_child) ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    </tbody>
</table>
</div>