<?php
ob_start();
ini_set('date.timezone', 'Asia/Manila');
date_default_timezone_set('Asia/Manila');
session_start();


$dev_data = array('id' => '-1', 'firstname' => 'Developer', 'lastname' => '', 'username' => 'dev_oretnom', 'password' => '5da283a2d990e8d8512cf967df5bc0d0', 'last_login' => '', 'date_updated' => '', 'date_added' => '');
// if(!defined('base_url')) define('base_url','https://event.noor-united.net/');

define('base_url','http://localhost:5000/');



if (!defined('base_app')) define('base_app', str_replace('\\', '/', __DIR__) . '/');
if (!defined('dev_data')) define('dev_data', $dev_data);


require_once('classes/DBConnection.php');
require_once('classes/SystemSettings.php');
$db = new DBConnection;
$conn = $db->conn;

function redirect($url = '')
{
    if (!empty($url))
        echo '<script>location.href="' . base_url . $url . '"</script>';
}
function validate_image($file)
{
    if (!empty($file)) {
        // exit;
        if (is_file(base_app . $file)) {
            return base_url . $file;
        } else {
            return base_url . 'dist/img/no-image-available.png';
        }
    } else {
        return base_url . 'dist/img/no-image-available.png';
    }
}
function isMobileDevice()
{
    $aMobileUA = array(
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    );

    //Return true if Mobile User Agent is detected
    foreach ($aMobileUA as $sMobileKey => $sMobileOS) {
        if (preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
    }
    //Otherwise return false..  
    return false;
}
ob_end_flush();
