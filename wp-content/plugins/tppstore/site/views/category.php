<?php
/**
 * User: leeparsons
 * Date: 15/12/2013
 * Time: 20:36
 */

require TPP_STORE_PLUGIN_DIR . 'helpers/category.php';

require TPP_STORE_PLUGIN_DIR . 'helpers/paginator.php';

$paginator = new TppStoreHelperPaginator();

$paginator->total_results = $total;

get_header('shop-category'); ?>
<?php /* $category->getChildren(); ?>
<?php if (count($category->children)): ?>
    <div class="wrap">

    <nav id="category_filters" class="wrap">
        <strong>Filter by  Category</strong>
        <span id="filter_toggle">Toggle</span>

        <?php foreach($category->children as $child): ?>
            <?php TppStoreCategoryHelper::getInstance()->renderChildren($child, $category, 1); ?>
        <?php endforeach; ?>

        <?php echo TppStoreCategoryHelper::getInstance()->renderAll(); ?>

        <!--        <div class="wrap">-->
        <!--        --><?php //foreach($category->children as $child): ?>
        <!--            --><?php //if ($child['child_id'] == $category->category_id || array_key_exists($category->category_id, $child['children'])): ?>
        <!--                --><?php //TppStoreCategoryRenderChild($child, $category, false, 2) ?>
        <!--            --><?php //endif; ?>
        <!--        --><?php //endforeach; ?>
        <!--        </div>-->
        <!--        <div class="wrap">-->
        <!--            --><?php //foreach($category->children as $child): ?>
        <!--                --><?php //if ($child['child_id'] == $category->category_id || array_key_exists($category->category_id, $child['children'])): ?>
        <!--                    --><?php //TppStoreCategoryRenderChild($child, $category, false, 3) ?>
        <!--                --><?php //endif; ?>
        <!--            --><?php //endforeach; ?>
        <!--        </div>-->
    </nav>
<?php endif; */ ?>
<?php TppStoreCategoryHelper::breadcrumb($category) ?>
</div>
    <div class="wrap" id="products">

        <!--<div class="aside-75">-->
        <div class="innerwrap">
            <header class="wrap">
                <h1 class="align-left"><?php echo $category->category_name ?> products</h1>
                <?php /*
                <div class="cat-filters align-right">
                    Filter Products:
                    <ul>
                        <li>
                            Downloads
                        </li>
                        <li>
                            Services
                        </li>
                        <li>
                            Products
                        </li>
                    </ul>
                    <?php $category->getChildren(); ?>
                    <?php if (count($category->children) > 0): ?>
                        <ul>
                        <?php foreach ($category->children as $category): ?>
                            <li><a href="" data-id="<?php echo $category['category_id'] ?>"><?php echo $category['category_name']; ?></a></li>
                        <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
                <?php */ ?>
            </header>

            <?php if (count($products) > 0): ?>
            <?php include TPP_STORE_PLUGIN_DIR . 'site/views/products/list.php' ?>
            <?php else: ?>
                <p>No Products listed for this category</p>
            <?php endif; ?>

        </div>
    </div>
<script>
    (function() {
        var toggler = {

            toggle: false,

            filters: false,

            hidden: false,

            init: function() {
                if (document.getElementById('category_filters')) {
                    toggler.toggle = document.getElementById('filter_toggle');
                    if (toggler.toggle != null) {
                        toggler.toggle.onclick = toggler.click;
                    }

                    toggler.filters = document.getElementById('category_filters').children;

                    toggler.toggleFilters(true);
                }
            },

            toggleFilters: function(hide) {
                if (toggler.filters.length > 0) {
                    for (var x in toggler.filters) {
                        if (typeof toggler.filters[x] == 'object' && toggler.filters[x].getAttribute('id') !== 'filter_toggle') {
                            //wrap div

                            if (toggler.filters[x].tagName == 'STRONG') {
                                continue;
                            }

                            if (toggler.filters[x].tagName == 'DIV') {
                                if (hide === true) {
                                    toggler.filters[x].style.width = 'auto';
                                    toggler.filters[x].style.marginTop = '0px';
                                } else {
                                    toggler.filters[x].style.width = '100%';
                                    toggler.filters[x].style.marginTop = '20px';
                                }
                            }
                            if ( toggler.filters[x].children.length > 0) {
                                for (var y in toggler.filters[x].children) {
                                    if (typeof toggler.filters[x].children[y] == 'object') {
                                        if (toggler.filters[x].children[y].getAttribute('class') != 'active') {
                                            if (hide === true) {
                                                toggler.filters[x].children[y].style.display = 'none';
                                            } else {
                                                toggler.filters[x].children[y].style.display = 'inline-block';
                                            }
                                        }
                                    }
                                }
                            } else if (toggler.filters[x].getAttribute('class') != 'active') {
                                //anchor
                                if (hide === true) {
                                    toggler.filters[x].style.display = 'none';
                                } else {
                                    toggler.filters[x].style.display = 'inline-block';
                                }
                            }
                        }

                    }
                }

                toggler.hidden = hide === false;
            },

            click: function() {
                toggler.toggleFilters(toggler.hidden);
            }

        }

        toggler.init();

    })();
</script>
<?php get_footer();