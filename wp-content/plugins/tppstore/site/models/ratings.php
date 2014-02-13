<?php
/**
 * User: leeparsons
 * Date: 05/01/2014
 * Time: 18:52
 */
 
class TppStoreModelRatings extends TppStoreAbstractModelResource {


    protected $_table = 'shop_reviews';

    public $average_ratings = array();

    public $product_id = null;

    /*
     * $rating = -1 to get all ratings, 0, 1, 2, 3, 4, 5 to get individual ratings, or array of ratings
     */
    public function getAverageRating($rating = -1)
    {
        $rating_hash = md5($rating);

        if (isset($this->average_ratings[$rating_hash])) {
            return $this->average_ratings[$rating_hash];
        }
            $this->average_ratings[$rating_hash] = array(
                'average'   =>  0,
                'reviews'   =>  0
            );

        if (false !== ($where = $this->generateWhere($rating))) {

                global $wpdb;

                $row = $wpdb->get_results(
                    "SELECT AVG(rating) AS average, COUNT(rating) AS c FROM " . $this->getTable() . $where
                );

                if ($wpdb->num_rows == 1) {


                    $avg = number_format($row[0]->average, 2);

                    $decimal = substr($avg, strpos($avg, '.') + 1);

                    if ($decimal  == '00') {
                        $avg = intval($avg);
                    } elseif (substr($decimal, 1) == '0') {
                        $avg = number_format($avg, 1);
                    }

                    $this->average_ratings[$rating_hash] = array(
                        'average'   =>  $avg,
                        'reviews'   =>  $row[0]->c
                    );
                }

            }

        return $this->average_ratings[$rating_hash];
    }

    public function getReviews($rating = -1)
    {

        $rating_hash = md5($rating);

        if (isset($this->average_ratings[$rating_hash]) && $this->average_ratings[$rating_hash]['reviews'] == 0) {
            return array();
        }

        $return = array();

        if (false !== ($where = $this->generateWhere($rating))) {
            global $wpdb;

            $rows = $wpdb->get_results(
                "SELECT review_id, u.user_id, u.first_name, u.last_name, u.src, rating, review_title, review_description FROM " . $this->getTable() . " AS r
                LEFT JOIN " . TppStoreModelUser::getInstance()->getTable() . " AS u ON u.user_id = r.user_id
                $where
                ORDER BY rating DESC
                ",
                OBJECT_K
            );

            if ($wpdb->num_rows > 0) {
                foreach ($rows as $row) {
                    $return[$row->review_id] = new TppStoreModelRating();
                    $return[$row->review_id]->setData($row);
                    $return[$row->review_id]->getUser()->setData(array(
                        'user_id'       =>  $row->user_id,
                        'first_name'    =>  $row->first_name,
                        'last_name'     =>  $row->last_name,
                        'src'           =>  $row->src
                    ));
                }
            }
        }


        return $return;

    }

    /*
     * $rating = -1 to get all ratings, 0, 1, 2, 3, 4, 5 to get individual ratings, or array of ratings
     */
    public function getRatingsByRating($rating = -1, $start = 0)
    {


        $limit = " LIMIT " . intval($start) . ",20 ";


        if (intval($this->product_id) == 0) {
            $this->ratings[$rating] = array();
        } else {
            if (is_array($rating)) {
                $where = " AND rating IN ('" . implode("','", $rating) . "') ";
            } else {
                switch (intval($rating)) {
                    case -1:
                        $where = "";
                        break;


                    default:
                        $where = " AND rating = " . $rating . " ";

                        break;
                }

            }

            global $wpdb;


            $rows = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM " . $this->getTable() . " WHERE product_id = %d $where $limit",
                    $this->product_id
                ),
                ARRAY_A
            );

            if ($wpdb->num_rows > 0) {

                $this->ratings[$rating]['average'] = 0;
                $this->ratings[$rating]['stars'] = array();

                foreach ($rows as $row) {
                    $this->ratings[$rating]['ratings'] = $row;
                }
            } else {
                $this->ratings[$rating] = array();
            }


        }
    }




    private function generateWhere($rating)
    {
        if (intval($this->product_id) == 0) {
            return false;
        } else {

            $where = " WHERE product_id = " . intval($this->product_id);


            if (is_array($rating)) {
                $where .= " AND rating IN ('" . implode("','", $rating) . "') ";
            } else {
                switch (intval($rating)) {
                    case -1:
                        //do nothing
                        break;

                    default:
                        $where .= " AND rating = " . $rating . " ";

                        break;
                }

            }
            return $where;
        }
    }

}