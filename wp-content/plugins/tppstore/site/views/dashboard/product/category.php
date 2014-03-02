<legend>category</legend>

<div class="form-group">

    <p>All our products are listed in categories. Please select which category best fits your product.</p>
    <?php

    $product_categories = $product->getCategories();

/*
    $product_category_1 = $product_category_index_2 = 0;

    ?>

    <label>Which category fits your product?</label>
    <select class="form-control category-list" id="category_1" name="category[]">
        <option value="">-- select category --</option>
        <?php foreach($categories as $category_id => $category): ?>
            <?php if ($category_id == 2) continue; ?>
            <option value="<?php echo $category_id ?>" <?php

            if (isset($product_categories[$category_id])) {
                echo 'selected="selected"';
                $product_category_1 = $category_id;
            }

            ?>><?php echo $category['category_name'] ?></option>
        <?php endforeach; ?>
    </select>
</div>

<div class="form-group <?php if (empty($categories[$product_category_1]['children'])): ?>hidden<?php endif; ?>">
    <label>Which sub category fits your product?</label>
    <select class="form-control category-list" id="category_2" name="category[]">
        <option value="">-- select category --</option>
        <?php if (!empty($categories[$product_category_1]['children'])): ?>
            <?php foreach ($categories[$product_category_1]['children'] as $child_index => $child): ?>
                <option value="<?php echo $child['category_id'] ?>" <?php


                if (isset($product_categories[$child['category_id']])) {
                    echo 'selected="selected"';
                    $product_category_index_2 = $child_index;
                }


                ?>><?php echo $child['category_name'] ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>

<div class="form-group <?php if (empty($categories[$product_category_1]['children'][$product_category_index_2])): ?>hidden<?php endif; ?>">
    <label>Which sub category fits your product?</label>
    <select class="form-control category-list" id="category_3" name="category[]">
        <option value="">-- select category --</option>
        <?php if (!empty($categories[$product_category_1]['children'][$product_category_index_2])): ?>
            <?php foreach ($categories[$product_category_1]['children'][$product_category_index_2]['children'] as $child): ?>
                <option value="<?php echo $child['category_id'] ?>" <?php


                if (isset($product_categories[$child['category_id']])) {
                    echo 'selected="selected"';
                }


                ?>><?php echo $child['category_name'] ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>
*/ ?>
<div class="form-group">
    <?php renderCatList($categories, $product_categories); ?>
</div>
<?php

function renderCatList($categories = array(), $product_categories, $level = 1, $parent = 0) { ?>
    <?php if (count($categories) > 0): ?>
        <ul class="cat-list">
<?php foreach ($categories as $category): ?>
    <li>
        <label class="wrap" for="cat_<?php echo $category['category_id']; ?>">

            <input data-level="<?php echo (string)$level; ?>" <?php echo is_array($product_categories) && isset($product_categories[$category['category_id']])?'checked="checked"':'' ?> type="checkbox" class="cats" data-parent="<?php echo $parent ?>" name="category[<?php echo $category['category_id'] ?>]" id="cat_<?php echo $category['category_id']; ?>" value="<?php echo $category['category_id'] ?>">
            <?php echo $category['category_name'] ?>
        </label>

        <?php if (!empty($category['children'])): ?>
            <?php renderCatList($category['children'], $product_categories, $level + 1, $category['category_id']); ?>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
    </ul>
        <?php endif; ?>

<?php } ?>
<script>
//    var category_options = <?php echo json_encode($categories); ?>;
</script>
