<?php
/**
 * User: leeparsons
 * Date: 17/02/2014
 * Time: 22:02
 */



class TppStoreModelEvent extends TppStoreModelProduct {

    public $listing_expire = null;
    public $event_start_date = null;
    public $event_end_date = null;
    public $lat = null;
    public $lng = null;
    public $address = null;

    protected $_event_table = 'shop_product_events';

    public function getFormattedListingExpireDate()
    {
        if (strtotime($this->listing_expire) == 0) {
            return false;
        }
        return $this->listing_expire;
    }

    public function getFormattedEventStartDate()
    {
        if (strtotime($this->event_start_date) == 0) {
            return false;
        }
        return $this->event_start_date;
    }

    private function setStrTimes()
    {

        if (is_null($this->end) && is_null($this->start)) {
            $this->end = strtotime($this->event_end_date);
            $this->start = strtotime($this->event_start_date);
        }

    }

    public function getEventDisplayDateRange()
    {
        $this->setStrTimes();

        if ($this->end > 0 && $this->end != $this->start) {
            return date('jS F, Y', $this->start) . ' - ' . date('jS F, Y', $this->end);
        } else {
            return date('jS F, Y', $this->start);
        }
    }

    public function getEventDuration()
    {
        $this->setStrTimes();

        if ($this->end > 0) {
            if ($this->end == $this->start) {
                return '1 day';
            } else {
                $date_time_zone = new DateTimeZone('Europe/London');
                $d1 = new DateTime($this->event_start_date, $date_time_zone);
                $d2 = new DateTime($this->event_end_date, $date_time_zone);
                $diff = date_diff($d1, $d2);
                return ($diff->days + 1) . ' days';
            }
        } else {
            return '1 day';
        }

    }

    public function hasEventDatePassed()
    {
        $d1 = new DateTime($this->event_start_date, new DateTimeZone('Europe/London'));
        $now = new DateTime('now', new DateTimeZone('Europe/London'));
        return $d1 <= $now;
    }


    public function getFormattedEventEndDate()
    {
        if (strtotime($this->event_end_date) == 0) {
            return false;
        }
        return $this->event_end_date;
    }



    public function getEventTable()
    {
        return $this->_event_table;
    }


    /*
     * This is called after the product id has been set, to get only data from the event table
     */
    public function loadEventData()
    {
        if (intval($this->product_id) <= 0) {
            $this->reset();
        } else {
            global $wpdb;
            $wpdb->query(
                $wpdb->prepare(
                    "SELECT * FROM " . $this->getEventTable() . " WHERE product_id = %d",
                    $this->product_id
                ),
                OBJECT_K
            );


            if ($wpdb->num_rows == 0) {
                $this->reset();
            } else {
                $this->setData($wpdb->last_result[0]);
            }

        }


        return $this;
    }

    public function readFromPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            parent::readFromPost();

            $this->event_start_date = filter_input(INPUT_POST, 'event_start_date', FILTER_SANITIZE_STRING);
            $this->event_end_date = filter_input(INPUT_POST, 'event_end_date', FILTER_SANITIZE_STRING);
            $this->listing_expire = filter_input(INPUT_POST, 'listing_expire', FILTER_SANITIZE_STRING);

            $this->address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);

            $this->lng = filter_input(INPUT_POST, 'lng', FILTER_SANITIZE_STRING);
            $this->lat = filter_input(INPUT_POST, 'lat', FILTER_SANITIZE_STRING);

            return true;
        } else {
            return false;
        }
    }

    public function save()
    {

        $saved_already = $this->saveImagesTemporarily();

        if (false === $this->validateEvent()) {
            return false;
        }

        $product_saved = parent::save();


        $event_saved = false;



        if (intval($this->product_id) > 0) {

            if (!is_null($this->address) && $this->address != '' && ($this->lng == '' && $this->lat == '')) {

                //figure out the latitude and longitude!
                //AIzaSyA6kw_hop7sLhFORdevW6i9tE73sTx_1M0
                $ch = curl_init('http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=' . urlencode($this->address));

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $res = curl_exec($ch);

                curl_close($ch);

                if (false !== ($res = json_decode($res))) {
                    if ($res->status == 'OK') {
                        if (property_exists($res, 'results') && isset($res->results[0])) {
                            if (property_exists($res->results[0], 'geometry')) {
                                $this->lat = $res->results[0]->geometry->location->lat;
                                $this->lng = $res->results[0]->geometry->location->lng;
                            }
                        }
                    }
                }

                //
            }

            global $wpdb;

            $wpdb->replace(
                $this->_event_table,
                array(
                    'event_start_date'  =>  $this->event_start_date,
                    'event_end_date'    =>  $this->event_end_date,
                    'listing_expire'    =>  $this->listing_expire,
                    'product_id'        =>  $this->product_id,
                    'address'           =>  $this->address,
                    'lat'               =>  $this->lat,
                    'lng'               =>  $this->lng
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                    '%s',
                    '%f',
                    '%f'
                )
            );

            $event_saved = $wpdb->result;

            if ($wpdb->result === false) {
                TppStoreMessages::getInstance()->addMessage('error', 'Unable to save your event. ' . $wpdb->last_error);
            }

        } else {
            TppStoreMessages::getInstance()->addMessage('error', 'Unable to save your event. Please fix the errors first.');
        }

        if ($product_saved === true && $event_saved === true) {
            $this->generateMap();
        }

        return $product_saved === true && $event_saved === true;



    }


    public function getMap($width = '', $height = '', $id = '')
    {
        if (file_exists(WP_CONTENT_DIR . '/uploads/store/' . $this->store_id . '/' . $this->product_id . '/maps/map.png')) {

            $width = $width == ''?'':' width="' . $width . '" ';

            $height = $height == ''?'':' height="' . $height . '" ';

            $id = $id == ''?'':' id="' . $id . '" ';

            return '<img ' . $id . ' ' . $width . ' ' . $height . ' src="/wp-content/uploads/store/' . $this->store_id . '/' . $this->product_id . '/maps/map.png" alt="event location" >';
        } else {
            return false;
        }
    }


    private function generateMap()
    {
        if (trim($this->lat) != '' && trim($this->lng) != '' && (floatval($this->lng) > 0 || floatval($this->lng) < 0) && (floatval($this->lat) < 0 || floatval($this->lat) > 0)) {
            if (false !== ($map = @file_get_contents('http://maps.googleapis.com/maps/api/staticmap?size=500x400&sensor=false&center=' . $this->lat . ',' . $this->lng . '&zoom=12&markers=%7Clabel:Event%20Location%7C' . $this->lat . ',' . $this->lng))) {
                $dir = new TppStoreDirectoryLibrary();

                $dir->setDirectory(WP_CONTENT_DIR . '/uploads/store/' . $this->store_id . '/' . $this->product_id . '/maps/');

                if (false !== $dir->createDirectory()) {

                    $path = WP_CONTENT_DIR . '/uploads/store/' . $this->store_id . '/' . $this->product_id . '/maps/map.png';

                    file_put_contents($path, $map);

                    @chmod($path, 0777);
                }
            }
        } else {
            @unlink(WP_CONTENT_DIR . '/uploads/store/' . $this->store_id . '/' . $this->product_id . '/maps/map.png');
        }

    }


    public function validateEvent()
    {

        $valid = parent::validate();

        $now = time();

        $event_start_timestr = strtotime(str_replace('/', '-', $this->event_start_date));

        $event_end_timestr = strtotime(str_replace('/', '-', $this->event_end_date));

        //$listing_expire_timestr = strtotime(str_replace('/', '-', $this->listing_expire));

        if ($this->event_start_date == '') {
            TppStoreMessages::getInstance()->addMessage('error', 'Please set the event start date.');
            $error = true;
        } elseif ($this->event_end_date != '' && $event_start_timestr > $event_end_timestr) {
            $this->event_end_date = $this->event_start_date;
            $event_end_timestr = $event_start_timestr;
            //TppStoreMessages::getInstance()->addMessage('error', 'Please set the start date before the end date.');
            //$error = true;
        }

//        if ($this->event_end_date != '' && $event_end_timestr < $now) {
//            TppStoreMessages::getInstance()->addMessage('error', 'The end time is in the past, this event will not be displayed on listings.');
//            $error = true;
//        }






        if ($event_end_timestr < $event_start_timestr) {
            $this->event_end_date = $this->event_start_date;
            $event_end_timestr = $event_start_timestr;
        }


//        if ($this->listing_expire != '' && $this->event_end_date != '' && $listing_expire_timestr > $event_end_timestr) {
//
//            $this->listing_expire = $this->event_end_date;
//        }

        $this->event_start_date = $event_start_timestr == 0?'':date('Y-m-d', strtotime(str_replace('/', '-', $this->event_start_date)));
        $this->event_end_date =  $event_end_timestr == 0?'':date('Y-m-d', strtotime(str_replace('/', '-', $this->event_end_date)));
        //$this->listing_expire =  $listing_expire_timestr == 0?'':date('Y-m-d', strtotime(str_replace('/', '-', $this->listing_expire)));


        if ($this->listing_expire == '') {

            $this->listing_expire = $this->event_end_date != ''?$this->event_end_date:$this->event_start_date;
        }
//        } elseif ($listing_expire_timestr <= $now) {
//            TppStoreMessages::getInstance()->addMessage('error', 'The listing expiry date is in the past. This event will not show in listings.');
//        }


        return !$error && $valid;
    }
}