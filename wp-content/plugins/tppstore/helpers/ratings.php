<?php
/**
 * User: leeparsons
 * Date: 05/01/2014
 * Time: 20:45
 */
 
class TppStoreHelperRatings {


    public function renderStars($rating = 0)
    {

        ?><div class="rating">

                <div class="bg" style="width:<?php

                echo 20 * $rating . '%';

                ?>"></div>

                <div class="star-rating"></div>
        </div><?php


    }


}