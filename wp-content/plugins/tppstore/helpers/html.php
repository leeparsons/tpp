<?php
/**
 * User: leeparsons
 * Date: 06/02/2014
 * Time: 22:36
 */
 
class TppStoreHelperHtml extends TppStoreAbstractInstantiable {


    private $og_images = array();

    /*
     * for open graph images
     */
    public function addOgImages($og_images = array())
    {
        if (is_array($og_images) && !empty($og_images)) {
            $this->og_images = $og_images;
        } elseif (is_string($og_images)) {
            $this->og_images[] = $og_images;
        }
    }


    public function renderOgImages()
    {
        if (!empty($this->og_images)) {
            foreach ($this->og_images as $image) {
                echo '<meta property="og:image" content="' . $image . '" />';
            }
        }
    }

}