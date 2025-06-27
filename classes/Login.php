<?php
require_once '../config.php';

class Login extends DBConnection 
{
    private $settings;
    
    public function __construct()
    {
        global $_settings;
        $this->settings = $_settings;
        
        parent::__construct();
        ini_set('display_errors', 1);
    }
    
    public function __destruct()
    {
        parent::__destruct();
    }
    
    public function index()
    {
        echo "<h1>Access Denied</h1> <a href='" . base_url . "'>Go Back.</a>";
    }
    
    public function login()
    {
        extract($_POST);
        
        // Input validation
        if (empty($username) || empty($password)) {
            return json_encode(array('status' => 'error', 'message' => 'Username and password are required'));
        }
        
        // Escape inputs to prevent SQL injection
        $username = $this->conn->real_escape_string($username);
        $password = $this->conn->real_escape_string($password);
        
        if (isset($manager) && $manager) {
            $qry = $this->conn->query("SELECT * FROM users WHERE username = '$username' AND password = MD5('$password') AND `type` = 0");
            
            if ($qry && $qry->num_rows > 0) {
                $user_data = $qry->fetch_array();
                foreach ($user_data as $k => $v) {
                    if (!is_numeric($k) && $k != 'password') {
                        $this->settings->set_userdata($k, $v);
                    }
                }
                $this->settings->set_userdata('login_type', 'event_manager');
                return json_encode(array('status' => 'success'));
            } else {
                return json_encode(array('status' => 'incorrect', 'message' => 'Invalid credentials'));
            }
        } else {
            $qry = $this->conn->query("SELECT * FROM users WHERE username = '$username' AND password = MD5('$password') AND `type` = 1");
            
            if ($qry && $qry->num_rows > 0) {
                $user_data = $qry->fetch_array();
                foreach ($user_data as $k => $v) {
                    if (!is_numeric($k) && $k != 'password') {
                        $this->settings->set_userdata($k, $v);
                    }
                }
                $this->settings->set_userdata('login_type', 1);
                return json_encode(array('status' => 'success'));
            } else {
                return json_encode(array('status' => 'incorrect', 'message' => 'Invalid credentials'));
            }
        }
    }
    
    public function elogin()
    {
        extract($_POST);
        
        // Input validation
        if (empty($code)) {
            return json_encode(array('status' => 'error', 'message' => 'Code is required'));
        }
        
        // Escape input to prevent SQL injection
        $code = $this->conn->real_escape_string($code);
        
        $qry = $this->conn->query("SELECT * FROM establishment WHERE code = '$code'");
        
        if ($qry && $qry->num_rows > 0) {
            $establishment_data = $qry->fetch_array();
            foreach ($establishment_data as $k => $v) {
                if (!is_numeric($k)) {
                    $this->settings->set_userdata($k, $v);
                }
            }
            $this->settings->set_userdata('login_type', 2);
            return json_encode(array('status' => 'success'));
        } else {
            return json_encode(array('status' => 'incorrect', 'message' => 'Invalid code'));
        }
    }
    
    public function rlogin()
    {
        extract($_POST);
        
        // Input validation
        if (empty($username)) {
            return json_encode(array('status' => 'error', 'message' => 'Username is required'));
        }
        
        // Escape input to prevent SQL injection
        $username = $this->conn->real_escape_string($username);
        
        // First try users table
        $qry = $this->conn->query("SELECT * FROM users WHERE username = '$username'");
        
        if ($qry && $qry->num_rows > 0) {
            $user_data = $qry->fetch_array();
            foreach ($user_data as $k => $v) {
                if (!is_numeric($k)) {
                    $this->settings->set_userdata($k, $v);
                }
            }
            $this->settings->set_userdata('login_type', 2);
            return json_encode(array('status' => 'success'));
        } else {
            // Try event_audience table
            $qry = $this->conn->query("SELECT * FROM event_audience WHERE contact = '$username'");
            
            if ($qry && $qry->num_rows > 0) {
                $audience_data = $qry->fetch_array();
                foreach ($audience_data as $k => $v) {
                    if (!is_numeric($k)) {
                        $this->settings->set_userdata($k, $v);
                    }
                }
                $this->settings->set_userdata('login_type', 2);
                return json_encode(array('status' => 'success'));
            } else {
                return json_encode(array('status' => 'incorrect', 'message' => 'User not found'));
            }
        }
    }
    
    public function logout()
    {
        if ($this->settings->sess_des()) {
            redirect('admin/login.php');
        }
    }
    
    public function rlogout()
    {
        if ($this->settings->sess_des()) {
            redirect('portal.php');
        }
    }
    
    public function elogout()
    {
        if ($this->settings->sess_des()) {
            redirect('portal.php');
        }
    }
}

// Handle actions
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();

switch ($action) {
    case 'login':
        echo $auth->login();
        break;
    case 'rlogin':
        echo $auth->rlogin();
        break;
    case 'elogin':
        echo $auth->elogin();
        break;
    case 'logout':
        echo $auth->logout();
        break;
    case 'elogout':
        echo $auth->elogout();
        break;
    case 'rlogout':
        echo $auth->rlogout();
        break;
    default:
        echo $auth->index();
        break;
}
?>