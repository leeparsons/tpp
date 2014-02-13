<?php
/**
 * User: leeparsons
 * Date: 24/12/2013
 * Time: 19:20
 */
 
class TppStoreBrowserLibrary extends TppStoreAbstractInstantiable {


    protected static $_browser = false;


    protected function __construct()
    {


        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/Opera/i', $_SERVER['HTTP_USER_AGENT']))
        {
            $browser_name = 'Microsoft Internet Explorer';
            $ub = "MSIE";
        }
        elseif(preg_match('/Firefox/i', $_SERVER['HTTP_USER_AGENT']))
        {
            $browser_name = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif(preg_match('/Chrome/i', $_SERVER['HTTP_USER_AGENT']))
        {
            $browser_name = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif(preg_match('/Safari/i', $_SERVER['HTTP_USER_AGENT']))
        {
            $browser_name = 'Apple Safari';
            $ub = "Safari";
        }
        elseif(preg_match('/Opera/i', $_SERVER['HTTP_USER_AGENT']))
        {
            $browser_name = 'Opera';
            $ub = "Opera";
        }
        elseif(preg_match('/Netscape/i', $_SERVER['HTTP_USER_AGENT']))
        {
            $browser_name = 'Netscape';
            $ub = "Netscape";
        }


        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $_SERVER['HTTP_USER_AGENT'], $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($_SERVER['HTTP_USER_AGENT'], "Version") < strripos($_SERVER['HTTP_USER_AGENT'], $ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if (is_null($version)) {
            $version = 0;
        }

        self::$_browser = array(
            'name'      =>  $browser_name,
            'version'   =>  $version
        );
    }

    public function getBrowserVersion()
    {
        return intval(self::$_browser['version']);
    }

    public function getBrowserName()
    {
        return self::$_browser['name'];
    }

}