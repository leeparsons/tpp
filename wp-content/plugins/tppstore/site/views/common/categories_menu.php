<?php

function TppRenderCategoryMenuLevel($categories, $level = 0) {

    if ($level > 1):
        return;
    endif;

    if ($level == 0):
        echo '<div class="main-menu-toggle" id="menu_toggle">Expand Menu</div><nav class="main-menu" id="main_menu">';

    endif;

    if (!empty($categories)):
        echo '<ul>';

        foreach ($categories as $category):

            if ($category['enabled'] == 0) {
                continue;
            }

            if ($level == 0 && $category['featured'] == 0) {
                continue;
            }

            echo '<li class="cat ' . $category['category_slug'] . '">';
            echo '<a href="/shop/category/' . $category['category_slug'] . '/">' . $category['category_name'] . '</a>';
            TppRenderCategoryMenuLevel($category['children'], $level+1);
            echo '</li>';
        endforeach;
        echo '</ul>';
    endif;

    if ($level == 0):
        echo '</nav>';
    endif;

}

TppRenderCategoryMenuLevel($categories, 0);

