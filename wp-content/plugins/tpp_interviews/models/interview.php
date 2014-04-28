<?php
/**
 * User: leeparsons
 * Date: 26/04/2014
 * Time: 21:07
 */
 

class TppInterviewModel {

    public $post_id = 0;
    public $interview_date = null;
    public $start_time = 0;
    public $end_time = 0;
    public $video = null;

    private static $_self = false;

    private $date_as_time_str = null;
    private $_table = 'tpp_interviews';
    private $_notices = array();

    /*
     * allow overriding of constructor to set up as a singleton, but this is not default so keep
     * constructor as public
     */
    public static function getInstance($post_id = false)
    {

        if (self::$_self === false) {
            self::$_self = new TppInterviewModel($post_id);
        }

        return self::$_self;

    }

    /*
     * Pass post ID if you have it at construction, otherwise pass it into setData
     */
    public function __construct($post_id = false)
    {
        if ($post_id !== false) {
            $this->post_id = $post_id;
        }

        //return itself for chaining
        return $this;
    }

    /*
     * return the date of the interview only
     */
    public function getDate($format = 'd-m-Y')
    {
        $this->convertDateToTimestamp();

        if (false === $this->date_as_time_str) {
            return '';
        }

        return date($format, $this->date_as_time_str);
    }

    /*
     * gets the start date time taking into account the start time
     */
    public function getStartDatetime($format = 'd M, Y h:i')
    {
        $this->convertDateToTimestamp('start_time');

        if (false === $this->date_as_time_str) {
            return '';
        }

        return date($format, strtotime($this->interview_date . ' ' . $this->start_time . ':00'));

    }




    /*
     * converts the date only to a unix timestamp
     */
    public function convertDateToTimestamp()
    {
        if (is_null($this->date_as_time_str)) {
            $this->date_as_time_str = strtotime($this->interview_date);
        }
    }

    /*
     * detects where or not the interview date and time means it's happened right now!
     */
    public function isLive()
    {
        if (trim($this->interview_date) == '') {
            return false;
        }

        $this->convertDateToTimestamp();

        if ($this->interview_date == date('Y-m-d')) {
            //same day but is the time between the start and end times?

            $start = new Datetime($this->interview_date . ' ' . $this->start_time . ':00', new DateTimeZone('Europe/London'));

            $end = new Datetime($this->interview_date . ' ' . $this->end_time . ':00', new DateTimeZone('Europe/London'));

            $now = new DateTime('now', new DateTimeZone('Europe/London'));

            return $start < $now && $end > $now;

        }

        return false;
    }


    /*
     * determine if the event has happened already
     */
    public function hasHappened()
    {

        if (trim($this->interview_date) == '') {
            return true;
        }

        $this->convertDateToTimestamp();

        $start_date = new DateTime($this->interview_date . ' ' . $this->start_time . ':00', new DateTimeZone('Europe/London'));
        $end_date = new DateTime($this->interview_date . ' ' . $this->end_time. ':00', new DateTimeZone('Europe/London'));
        $now = new DateTime('now', new DateTimeZone('Europe/London'));

        if ($end_date > $now && $start_date < $now) {
            return false;
        } elseif ($end_date < $now) {
            return true;
        } elseif ($end_date > $now) {
            return false;
        }
    }



    /*
     * check is the video has been set
     */
    public function hasVideo()
    {
        return strlen(trim($this->video)) > 0;
    }


    /*
     * returns the actual embed code
     */
    public function getVideoEmbedCode($overlay = false, $width = false, $height = false)
    {


        if ($width === false && $height === false) {
            $width = 320;
            $height = 240;
        } elseif ($width !== false && $height === false) {
            $height = $width * 240 / 320;
        } elseif ($width === false && $height !== false) {
            $width = $height * 320 / 240;
        }

        if (false === strpos($this->video, 'iframe')) {
            //create the iframe

            $step1 = explode('v=', $this->video);
            $step2 = explode('&', $step1[1]);
            $video_id = $step2[0];

            $src = "http://www.youtube.com/embed/$video_id";

        } else {
            $doc = new DOMDocument();
            $doc->loadHTML($this->video);
            $src = $doc->getElementsByTagName('iframe')->item(0)->getAttribute('src');

            if (false !== ($pos = strpos($src, '?'))) {
                $src = substr($src, 0, $pos);
            }
        }



        $this->video = '<iframe id="video_' . $this->post_id . '" width="320" height="240" src="' . $src . '?autoplay=0&controls=2&modestbranding=1&rel=0&showinfo=0&wmode=transparent" frameborder="0"></iframe>';

        $this->video = preg_replace(
            array('/width="\d+"/i', '/height="\d+"/i'),
            array(sprintf('width="%d"', $width), sprintf('height="%d"', $height)),
            $this->video);

        if (true === $overlay) {


            return '<div class="video-wrap" style="width:' . $width . 'px;height:' . $height . 'px;"><a href="javascript:return false" class="video-mask"></a>' . $this->video . '</div>';
        } else {
            return $this->video;
        }


    }

    /*
     * Use this to set variables on this class in bulk
     */
    public function setData($data = array())
    {

        foreach ($data as $key  =>  $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        return $this;
    }

    /*
     * Resets all variables of this class to null
     */
    public function reset()
    {

        $reflect = new ReflectionClass($this);
        $vars = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($vars as $key) {
            $this->$key = null;
        }
    }

    /*
     * read from post data - used in admin area
     */
    public function readFromPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $this->interview_date = filter_input(INPUT_POST, 'interview_date', FILTER_SANITIZE_STRING);

            $this->start_time = filter_input(INPUT_POST, 'interview_start_time', FILTER_SANITIZE_STRING);

            $this->end_time = filter_input(INPUT_POST, 'interview_end_time', FILTER_SANITIZE_STRING);

            $this->video = filter_input(INPUT_POST, 'interview_video', FILTER_UNSAFE_RAW);

            return $this;
        } else {
            return false;
        }
    }

    /*
     * Loads the interview data for the post id set
     */
    public function load()
    {

        if (intval($this->post_id) > 0) {

            global $wpdb;

            $wpdb->query(
                $wpdb->prepare(
                    "SELECT * FROM " . $this->_table . " WHERE post_id = %d",
                    array(
                        $this->post_id
                    )
                ),
                OBJECT_K
            );

            if ($wpdb->num_rows > 0) {
                $this->setData($wpdb->last_result[0]);
            }

        } else {
            $this->reset();
        }

        return $this;
    }

    /*
     * save the interview information
     */
    public function save()
    {

        if (false !== $this->validate()) {


            global $wpdb;

            $wpdb->replace(
                $this->_table,
                array(
                    'interview_date'    =>  $this->interview_date,
                    'start_time'        =>  $this->start_time,
                    'end_time'          =>  $this->end_time,
                    'video'             =>  $this->video,
                    'post_id'           =>  $this->post_id
                ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%d'
                )
            );

            if (false === $wpdb->result) {
                $this->_notices[] = 'Interview Details not saved!';
                $this->_notices[] = $wpdb->last_error;
            }

            return $wpdb->result;

        } else {
            $this->saveNotices();
            return false;
        }

    }

    /*
     * saves notices into session
     */
    public function saveNotices()
    {

        if (count($this->_notices) > 0) {
            ob_start();
            if (!session_id()) {
                session_start();
            }
            ob_end_clean();

            $_SESSION['TPP_INTERVIEWS_NOTICES'] = $this->_notices;

        }

    }

    /*
     * get any notices stored
     */
    public function getNotices()
    {
        if (count($this->_notices) == 0) {
            ob_start();
            if (!session_id()) {
                session_start();
            }
            ob_end_clean();

            if (isset($_SESSION['TPP_INTERVIEWS_NOTICES'])) {
                $this->_notices = $_SESSION['TPP_INTERVIEWS_NOTICES'];
            }

        }


        return $this->_notices;

    }

    /*
     * delete notices saved in session
     */
    public function deleteNotices()
    {

        ob_start();
        if (!session_id()) {
            session_start();
        }
        ob_end_clean();

        if (isset($_SESSION['TPP_INTERVIEWS_NOTICES'])) {
            $_SESSION['TPP_INTERVIEWS_NOTICES'] = array();
            unset($_SESSION['TPP_INTERVIEWS_NOTICES']);
        }



    }

    /*
     * validate the required fields
     */
    private function validate()
    {
        if (intval($this->post_id) < 1) {
            $error = true;
            $this->_notices[] = 'Please create a post first';
        }

        if (trim($this->end_time) == '') {
            $error = true;
            $this->_notices[] = 'Please enter an end time for the interview';
        }

        if (trim($this->end_time) == '') {
            $error = true;
            $this->_notices[] = 'Please enter a start time for the interview';
        }

        if (trim($this->interview_date) == '') {
            $error = true;
            $this->_notices[] = 'Please enter the date for the interview';
        } else {
            $this->interview_date = date('Y-m-d', strtotime($this->interview_date));
        }


        if (trim($this->video) == '') {
            $error = true;
            $this->_notices[] = 'Please enter a video embed code';
        }

        if ($error === true) {

            array_unshift($this->_notices, 'Interview Details not saved!');

        }

        return !$error;
    }

}