<?php
/**
 * User: leeparsons
 * Date: 01/12/2013
 * Time: 15:38
 */
class TppStoreModelUser extends TppStoreAbstractModelBase
{

    public $user_id = null;
    public $first_name = null;
    public $last_name = null;
    public $email = null;
    public $address = null;
    public $telephone = null;
    public $enabled = 0;
    public $activation = null;
    public $date_activated = null;
    public $password = null;
    public $facebook_user_id = null;
    public $user_type = 'buyer';
    public $last_visit = null;
    public $user_src = null;
    public $bio = null;
    public $title = null;
    public $gender = null;
    public $last_dashboard_visit = null;

    protected $_confirm_password = false;

    protected $_table = 'shop_users';

    protected static $_instance = false;

    protected $_sizes = array(
        'thumb' =>  array(
            'width'     =>  250,
            'height'    =>  250
        )
    );


    public static function getInstance()
    {
        if (false === self::$_instance) {
            $class = get_called_class();
            self::$_instance = new $class();
        }
        return self::$_instance;
    }

    public function getName($with_title = false)
    {
        if (true === $with_title) {
            return $this->title . ' ' . trim($this->first_name . ' ' . $this->last_name);
        } else {
            return trim($this->first_name . ' ' . $this->last_name);
        }
    }

    public function getTitle()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getSeoTitle()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getDescription()
    {
        return esc_attr($this->bio);
    }

    public function getSeoDescription()
    {
        return esc_attr($this->bio);
    }

    public function getSizes()
    {
        return $this->_sizes;
    }

    public function isLoggedIn()
    {
        return false !== TppStoreControllerUser::getInstance()->loadUserFromSession();
    }

    public function getImageDirectory($create = false)
    {
        if (!class_exists('TppStoreDirectoryLibrary')) {
            include TPP_STORE_PLUGIN_DIR . 'libraries/directory.php';
        }

        //determine the save path
        $directory = new TppStoreDirectoryLibrary();

        $directory->setDirectory(WP_CONTENT_DIR . '/uploads/store/users/' . $this->user_id . '/');

        if (false === $directory->directoryExists()) {

            if (false === $create) {
                return false;
            } else if (false === $directory->createDirectory()) {
                return false;
            }
        }

        return WP_CONTENT_DIR . '/uploads/store/users/' . $this->user_id . '/';


    }

    public function getPermalink()
    {
        return '/shop/profile/' . $this->user_id . '/';
    }

    public function getSrc($size = false, $html = false, $link = false)
    {



        if (trim($this->user_src) == '' || intval($this->user_id) <= 0) {
            return false;
        }


        if (false === ($directory = $this->getImageDirectory())) {
            return false;
        }

        $relative_directory = substr($directory, strlen(WP_CONTENT_DIR . '/uploads'));

        if (false !== $size) {
            $size = TppStoreModelProductImages::getSize($size);

            $lib = new TppStoreLibraryFileImage();
            $lib->setFile($directory . $this->user_src);
            $file = $lib->getBaseName() . '_' . $size['width'] . '_' . $size['height'] . '.' . $lib->getExtension();

            if (!file_exists($file)) {
                if (false === $lib->resize($size)) {
                    return false;
                }
            }

            $this->user_src = substr($lib->getBaseName() . '_' . $size['width'] . '_' . $size['height'] . '.' . $lib->getExtension(), strlen($directory));
        }



        if ($html === true) {

            if ($link === true) {
                return '<a href="' . $this->getPermalink() . '"><img src="' . $relative_directory . $this->user_src . '" alt="' . esc_attr($this->first_name . ' ' . $this->last_name) . '"></a>';

            } else {
                return '<img src="' . $relative_directory . $this->user_src . '" alt="' . esc_attr($this->first_name . ' ' . $this->last_name) . '">';
            }


        } else {
            return $relative_directory . $this->user_src;
        }

    }

    public function getUserByID($enabled = 'all')
    {

        if (intval($this->user_id) <= 0) {
            $this->reset();
            return $this;
        }


        switch ($enabled) {
            case '1':
                $where = " AND enabled = 1";
                break;
            case '0':
                $where = " AND enabled = 0";
                break;

            default:
                $where = "";
                break;
        }

        global $wpdb;

        $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM " . $this->getTable() . " WHERE user_id = %d $where",
                $this->user_id
            ),
            OBJECT_K
        );

        if ($wpdb->num_rows == 1) {
            $this->setData($wpdb->last_result[0]);
            return $this;
        } else {
            return false;
        }
    }

    public function getUserByEmail()
    {


        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        global $wpdb;

        $res = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM " . $this->getTable() . " WHERE email = %s",
                $this->email
            )
        );

        if ($wpdb->num_rows == 1) {
            foreach ($res as $row) {
                foreach ($row as $key => $value) {
                    if (is_null($this->$key)) {
                        $this->$key = $value;
                    } elseif ($key == 'user_type') {
                        $this->user_type = $value;
                    } elseif ($key == 'enabled') {
                        $this->enabled = $value;
                    }
                }
            }
        }

    }

    public function getUserByFacebookID()
    {

        if (intval($this->facebook_user_id) <= 0) {
            return false;
        }

        global $wpdb;

        $res = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM " . $this->getTable() . " WHERE facebook_user_id = %d",
                $this->facebook_user_id
            )
        );

        if ($wpdb->num_rows == 1) {
            foreach ($res as $row) {
                foreach ($row as $key => $value) {
                    if (is_null($this->$key)) {
                        $this->$key = $value;
                    } elseif ($key == 'user_type' || $key == 'enabled' || $key == 'email') {
                        $this->$key = $value;
                    }
                }
            }
        }

        return $this;

    }

    public function getProfilePic()
    {
        return false;
    }

    public function generateNewPassword()
    {
        $this->password = $this->generatePassword();
        $this->_confirm_password = $this->password;
    }

    public function readFromPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->first_name = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);

            $this->last_name = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);

            $this->email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);

            $this->telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);

            $this->address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);

            $this->title= filter_input(INPUT_POST, 'u_title', FILTER_SANITIZE_STRING);

            $this->gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);

            $src = filter_input(INPUT_POST, 'uploaded_pic', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
            $this->user_src = null;

            if (is_array($src) && !empty($src[0])) {
                $this->user_src = $src[0];
            } else {
                $original_pic = filter_input(INPUT_POST, 'original_pic', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
                if (is_array($original_pic) && !empty($original_pic[0])) {
                    $this->user_src = $original_pic[0];
                }
            }

            $password = filter_input(INPUT_POST, 'pswd', FILTER_SANITIZE_STRING);
            $confirm = filter_input(INPUT_POST, 'cpswd', FILTER_SANITIZE_STRING);

            if ($password || $confirm) {
                $this->password = $password;
                $this->_confirm_password = $confirm;
            }

            return true;
        } else {
            return false;
        }
    }

    public function authenticate()
    {
        if (!$this->validate(false, true)) {
            return false;
        }

        //now figure out if this user exists
        global $wpdb;


        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM " . $this->getTable() . " WHERE email = %s AND password = %s",
                array(
                    $this->email,
                    md5($this->password . AUTH_KEY . AUTH_SALT)
                )
            )
        );

        if ($wpdb->num_rows  == 1) {

            return $this->setData($row);

        } else {
            TppStoreMessages::getInstance()->addMessage('error', array('user'   =>  'Sorry, the username and password combination was not recognised'));
            return false;
        }


    }

    public function save($search_email = true, $save_password = true)
    {

        if (!$this->validate($search_email)) {
            return false;
        }

        global $wpdb;

        $save_array = array(
            'first_name'            =>  $this->first_name,
            'last_name'             =>  $this->last_name,
            'facebook_user_id'      =>  $this->facebook_user_id,
            'enabled'               =>  $this->enabled,
            'activation'            =>  $this->activation,
            'email'                 =>  $this->email,
            'address'               =>  $this->address,
            'telephone'             =>  $this->telephone,
            'user_type'             =>  $this->user_type,
            'date_activated'        =>  $this->date_activated?:date('Y-m-d H:i:s'),
            'user_src'              =>  $this->user_src,
            'title'                 =>  $this->title,
            'gender'                =>  $this->gender,
            'last_dashboard_visit'  =>  $this->last_dashboard_visit
        );
        $save_array_replace = array(
            '%s',
            '%s',
            '%s',
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
        );

        $inserted = false;
        $send_email = false;

        if (intval($this->user_id) <= 0) {
            //insert

            $inserted = true;

            if ($this->enabled == 0) {
                $this->activation = uniqid('shop_');
            }


            if (is_null($this->password)) {
                //send an email confirmation about this password
                $send_email = true;
                $this->password = $this->generatePassword();
                $this->_confirm_password = $this->password;
            }




            if (true === $save_password) {
                $save_array['password'] = md5($this->password . AUTH_KEY . AUTH_SALT);
                $save_array_replace[] = '%s';
            }

            $wpdb->insert(
                $this->getTable(),
                $save_array,
                $save_array_replace
            );

            if ($wpdb->rows_affected == 1) {
                $this->user_id = $wpdb->insert_id;
            }

        } else {
            //update

            if (true === $save_password && $this->password === $this->_confirm_password) {

                $save_array['password'] = md5($this->password . AUTH_KEY . AUTH_SALT);
                $save_array_replace[] = '%s';
            }

            $wpdb->update(
                $this->getTable(),
                $save_array,
                array(
                    'user_id'   =>  $this->user_id
                ),
                $save_array_replace,
                '%d'
            );
        }


        if ($wpdb->result === true) {
            if (false !== ($save_path = $this->getImageDirectory(true))) {

                //now we need to delete all files that do not match this file!
                $directory= new TppStoreDirectoryLibrary();

                $directory->setDirectory($save_path);

                $files = $directory->getFiles(false);

                $images_to_keep = array();

                if (!is_null($this->user_src)) {
                    $tmp = TppStoreModelUser::getSizes();
                    $images_to_keep = array(
                        $this->user_src
                    );

                    $image = new TppStoreLibraryFileImage();

                    $image->setFile($this->user_src);
                    $extension = $image->getExtension();
                    $base_name = $image->getBaseName();

                    foreach ($tmp as $size) {
                        if (is_array($size)) {
                            $images_to_keep[] = $base_name . '_' . $size['width'] . '_' .  $size['height'] . '.' . $extension;
                        }
                    }
                }

                if (count($files) > 0) {
                    foreach ($files as $file) {
                        if (!in_array($file, $images_to_keep)) {
                            @unlink($save_path . $file);
                        }
                    }
                }
            }

        }

        if (true === $send_email && $wpdb->result === true && !is_null($this->email) && $this->email != '') {

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
            $headers .= "From: Rosie Parsons <rosie@thephotographyparlour.com>" . "\r\n";
            $headers .= "Reply-To: The Photography Parlour <rosie@thephotographyparlour.com>\r\n";
            $headers .= "Return-Path: The Photography Parlour  <rosie@thephotographyparlour.com>\r\n";

            $headers .= "Organization: The Photography parlour\r\n";
            $headers .= "X-Priority: 3\r\n";
            $headers .= "X-Mailer: PHP". phpversion() ."\r\n";

            $signature = "
                <table>
                <tbody>
                <tr>
                <td><p>Thanks!</p></td>
                </tr>
                <tr>
                <td><p>Rosie Parsons</p></td>
                </tr>
                <tr>
                <td><p>The Photography Parlour</p></td>
                </tr>
                <tr>
                <td><p>www.thephotographyparlour.com</p></td>
                </tr>
                <tr>
                <td>rosie@thephotographyparlour.com</td>
                </tr>
                <tr>
                <td><p>@photoparlour</p></td>
                </tr>";

            if ($inserted === true) {

                mail(
                    $this->email,
                    'Your password has been created',
                    '<p>Hello ' . trim($this->first_name) . "</p><p>" . 'Your password for your account on The Photography Parlour has been created.<br><br>Your password is: ' . $this->password . '.' . "<br></p>" .

                    "<p>We recommend you login and change your password to something memorable.</p>" .

                    "<p><a href=" . get_site_url(null, 'shop/store_login') . '?redirect=' . urlencode('/shop/myaccount/profile/edit/#change_password') . "click here to sign in</a> and change your password under your profile menu.<br></p>" . $signature,
                    $headers,
                    "-frosie@thephotographyparlour.com"
                );
            } else {
                mail(
                    $this->email,
                    'Your password has been updated',
                    '<p>Hello ' . trim($this->first_name) . "</p><p>" . 'Your password for your account on The Photography Parlour has been updated.<br><br>Your new password is: ' . $this->password . '.' . "<br></p>" .

                    "<p><a href=" . get_site_url(null, 'shop/store_login') . '?redirect=' . urlencode('/shop/myaccount/profile/edit/#change_password') . ">Click here to sign in</a> and change your password under your profile menu.<br></p>" . $signature,
                    $headers,
                    "-frosie@thephotographyparlour.com"
                );
            }

        }

        return $wpdb->result;


    }

    public function getLastVisit()
    {
        if (intval($this->user_id) > 0 && !is_null($this->last_visit)) {

            $visit = new DateTime($this->last_visit);

            $diff = $visit->diff(new Datetime());

            if ($diff->y > 0) {
                $diff_string = $diff->y . ' years';
            } elseif ($diff->m > 0) {
                $diff_string = $diff->m . ' months';
            } elseif ($diff->d > 0) {
                $diff_string = $diff->d . ' days';
            } else {
                $diff_string = false;
            }

            return $diff_string;

        } else {
            return false;
        }
    }

    public function updateLastVisit()
    {
        if (intval($this->user_id) > 0) {
            global $wpdb;

            $wpdb->query(
                $wpdb->prepare(
                   "UPDATE " . $this->getTable() . " SET last_visit = NOW() WHERE user_id = %d",
                    $this->user_id
                )
            );

        }
    }

    public function validate($search_email = true, $login_validation = false)
    {
        $error = false;

        if (false === $login_validation && trim($this->first_name) == '') {
            TppStoreMessages::getInstance()->addMessage('error', array('first_name' => 'Please enter your first name'));
            $error = true;
        }

        if (false === $login_validation && trim($this->last_name) == '') {
            TppStoreMessages::getInstance()->addMessage('error', array('last_name'  =>'Please enter your last name'));
            $error = true;
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL) && intval($this->facebook_user_id) <= 0) {
            TppStoreMessages::getInstance()->addMessage('error', array('email'  =>  'Please enter a valid email address'));
            $error = true;
        }

        if (false === $login_validation && true === $search_email && false === $error && !is_null($this->email)) {
            //determine if this email is already registered?
            global $wpdb;

            $wpdb->query($wpdb->prepare(
                "SELECT user_id FROM " . $this->getTable() . " WHERE email = %s AND user_id <> %d",
                array(
                    $this->email,
                    $this->user_id
                )
            ));

            if ($wpdb->num_rows > 0) {
                TppStoreMessages::getInstance()->addMessage('error', array('email'  =>  'Your email address has already been registered'));
                $error = true;
            }
        }

        if (false !== $this->_confirm_password) {
            if ($this->_confirm_password !== $this->password) {
                TppStoreMessages::getInstance()->addMessage('error', array('password'   =>  'Your passwords did not match'));
                $error = true;
            }
        }

        return !$error;
    }

    private function generatePassword()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 10; $i++) {
            $randstring .= $characters[rand(0, strlen($characters))];
        }
        return $randstring;
    }

}