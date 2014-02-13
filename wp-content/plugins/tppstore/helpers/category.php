<?php



class TppStoreCategoryHelper extends TppStoreAbstractInstantiable {

    private $level_1 = array();
    private $level_2 = array();
    private $level_3 = array();


    public function addLevel1($link = '')
    {
        $this->level_1[] = $link;
    }

    public function addLevel3($link = '')
    {
        $this->level_3[] = $link;
    }


    public function addLevel2($link = '')
    {
        $this->level_2[] = $link;
    }

    public function renderAll()
    {
        $html = array();

        if (!empty($this->level_1)) {
            $html[] = implode('', $this->level_1);
        }

        if (!empty($this->level_2)) {
            $html[] = '<div class="wrap level-break">';
            $html[] = implode('', $this->level_2);
            $html[] = '</div>';
        }

        if (!empty($this->level_3)) {
            $html[] = '<div class="wrap level-break">';
            $html[] = implode('', $this->level_3);
            $html[] = '</div>';
        }

        return implode('', $html);
    }

    private function inActiveCategory($tree = array(), $active_parent = false)
    {
        if (is_array($active_parent) && !empty($active_parent) && $tree['parent_id'] == $active_parent['category_id'] && array_key_exists($tree['category_id'], $active_parent['children'])) {
            return true;
        } elseif (false === $active_parent) {

            //top level
            return true;

        }
        return false;
    }

    public function inCategory($category_id = 0, $parent = array(), $grandchild= false)
    {

        if (isset($parent['category_id']) && $parent['category_id'] == $category_id) {
            return true;
        }


        if (isset($parent['children']) && is_array($parent['children']) && !empty($parent['children'])) {

            if (array_key_exists($category_id, $parent['children'])) {
                return true;
            }

            return $this->inCategory($category_id, $parent['children'], true);

        } elseif ($grandchild === true && is_array($parent)) {
            foreach ($parent as $_cat) {
                if (isset($_cat['children']) && is_array($_cat['children'])) {
                    if (array_key_exists($category_id, $_cat['children'])) {
                        return true;
                    }
                }
            }

        }

        return false;

    }

    public function renderChildren($category_tree = array(), $current_displayed_category, $render_level = 1, $parent= false) {

        //category tree contains the heirarchical list of categories.
        //currently displayed category is eth category we have browsed to.
        //render level is the level of heirarchy we are on in the menu

        $active_item = false;


        if ($render_level == $category_tree['level'] && $this->inActiveCategory($category_tree, $parent)) {

            //never comes in here for level 3

            if (
                $current_displayed_category->category_id == $category_tree['category_id']
                ||
                $this->inCategory($current_displayed_category->category_id, $category_tree)
            ) {
                $active_item = true;
            }


            $this->{'addLevel' . $render_level}('<a ' . ($active_item?'class="active"':'') . ' href="/shop/category/' . $category_tree['category_slug'] . '">' . $category_tree['category_name'] . ' (' . $category_tree['product_count'] . ')</a>');
        }

        if (is_array($parent) && $render_level == 3) {

            //this is the currently highlighted 2nd level category
            if ($current_displayed_category->category_id == $category_tree['parent_id'] || $category_tree['category_id'] == $current_displayed_category->category_id) {
                $active_item =
                    is_array($parent['children']) &&
                    isset($parent['children'][$category_tree['parent_id']]) &&
                    is_array($parent['children'][$category_tree['parent_id']]['children']) &&
                    isset($parent['children'][$category_tree['parent_id']]['children'][$category_tree['category_id']])
                    &&
                    isset($parent['children'][$category_tree['parent_id']]['children'][$current_displayed_category->category_id])

                ;
                $this->{'addLevel' . $render_level}('<a ' . ($active_item?'class="active"':'') . ' href="/shop/category/' . $category_tree['category_slug'] . '">' . $category_tree['category_name'] . ' (' . $category_tree['product_count'] . ')</a>');
            } else {
                if (
                    isset($parent['children']) && is_array($parent['children']) &&
                    isset($parent['children'][$category_tree['parent_id']]) &&
                    is_array($parent['children'][$category_tree['parent_id']]['children']) &&
                    isset($parent['children'][$category_tree['parent_id']]['children'][$category_tree['category_id']])
                    &&
                    isset($parent['children'][$category_tree['parent_id']]['children'][$current_displayed_category->category_id])
                ) {

                    $this->{'addLevel' . $render_level}('<a href="/shop/category/' . $category_tree['category_slug'] . '">' . $category_tree['category_name'] . ' (' . $category_tree['product_count'] . ')</a>');
                }
            }

            //determine if the current category is in this category?

            // level 3 - just add the relevant links!
//            foreach($category_tree['children'] as $grandchild):
//                $this->{'addLevel' . $render_level}('<a ' . ($active_item?'class="active"':'') . ' href="/shop/category/' . $category_tree['category_slug'] . '">' . $category_tree['category_name'] . ' (' . $category_tree['product_count'] . ')</a>');
//            endforeach;


        } else {
            if (isset($category_tree['children']) && is_array($category_tree['children'])) {

                if (empty($parent) && $render_level == 1 && $active_item) {
                    $parent = $category_tree;
                } else {
                    if (false === $parent) {
                        $parent = array();
                    }
                }
                foreach($category_tree['children'] as $grandchild):
                    $this->renderChildren($grandchild, $current_displayed_category, $render_level+1, $parent);
                endforeach;


            }
        }

    }

    public static function breadcrumb($category = false)
    {

        $parents = TppStoreModelCategory::getInstance()->getParents($category->category_id);


        $breadcrumbs = array();

        $breadcrumbs[] = '<a href="/shop">Shop</a>';


        if (!is_null($parents->grand_parent_slug) && !is_null($parents->grand_parent_name)) {
            $breadcrumbs[] = '<a href="/shop/category/' . $parents->grand_parent_slug . '">' . $parents->grand_parent_name  . '</a>';
            if (!is_null($parents->parent_slug) && !is_null($parents->parent_name)) {
                $breadcrumbs[] = '<a href="/shop/category/' . $parents->grand_parent_slug . '/' . $parents->parent_slug . '">' . $parents->parent_name  . '</a>';
            }
        } elseif (!is_null($parents->parent_slug) && !is_null($parents->parent_name)) {
            $breadcrumbs[] = '<a href="/shop/category/' . $parents->parent_slug . '">' . $parents->parent_name  . '</a>';
        }

        $breadcrumbs[] = '<span>' . $category->category_name . '</span>';

        echo implode(' / ', $breadcrumbs);

    }

}

