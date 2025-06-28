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
        
        // بررسی اتصال به دیتابیس
        if (!$this->conn) {
            error_log("Database connection failed");
            die("Database connection error");
        }
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
        // بررسی وجود POST data
        if (empty($_POST)) {
            return json_encode(array('status' => 'error', 'message' => 'No data received'));
        }
        
        // لاگ کردن داده‌های ورودی برای دیباگ
        error_log("Login attempt: " . json_encode($_POST));
        
        // استخراج متغیرها با بررسی وجود
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $manager = isset($_POST['manager']) ? $_POST['manager'] : false;
        
        // Input validation
        if (empty($username) || empty($password)) {
            return json_encode(array('status' => 'error', 'message' => 'Username and password are required'));
        }
        
        // Escape inputs to prevent SQL injection
        $username = $this->conn->real_escape_string($username);
        $password = $this->conn->real_escape_string($password);
        
        try {
            if ($manager) {
                // لاگین مدیر
                $sql = "SELECT * FROM users WHERE username = '$username' AND password = MD5('$password') AND `type` = 0";
                $qry = $this->conn->query($sql);
                
                // لاگ کردن کوئری برای دیباگ
                error_log("Manager login query: " . $sql);
                
                if ($qry && $qry->num_rows > 0) {
                    $user_data = $qry->fetch_array();
                    
                    // تنظیم session data
                    foreach ($user_data as $k => $v) {
                        if (!is_numeric($k) && $k != 'password') {
                            $this->settings->set_userdata($k, $v);
                        }
                    }
                    $this->settings->set_userdata('login_type', 'event_manager');
                    
                    error_log("Manager login successful for user: " . $username);
                    return json_encode(array('status' => 'success', 'redirect' => 'admin/'));
                } else {
                    error_log("Manager login failed for user: " . $username);
                    return json_encode(array('status' => 'incorrect', 'message' => 'Invalid credentials'));
                }
            } else {
                // لاگین کاربر عادی
                $sql = "SELECT * FROM users WHERE username = '$username' AND password = MD5('$password') AND `type` = 1";
                $qry = $this->conn->query($sql);
                
                // لاگ کردن کوئری برای دیباگ
                error_log("User login query: " . $sql);
                
                if ($qry && $qry->num_rows > 0) {
                    $user_data = $qry->fetch_array();
                    
                    // تنظیم session data
                    foreach ($user_data as $k => $v) {
                        if (!is_numeric($k) && $k != 'password') {
                            $this->settings->set_userdata($k, $v);
                        }
                    }
                    $this->settings->set_userdata('login_type', 1);
                    
                    error_log("User login successful for user: " . $username);
                    return json_encode(array('status' => 'success', 'redirect' => 'dashboard/'));
                } else {
                    error_log("User login failed for user: " . $username);
                    return json_encode(array('status' => 'incorrect', 'message' => 'Invalid credentials'));
                }
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return json_encode(array('status' => 'error', 'message' => 'Login failed due to system error'));
        }
    }
    
    public function elogin()
    {
        // بررسی وجود POST data
        if (empty($_POST)) {
            return json_encode(array('status' => 'error', 'message' => 'No data received'));
        }
        
        $code = isset($_POST['code']) ? trim($_POST['code']) : '';
        
        // Input validation
        if (empty($code)) {
            return json_encode(array('status' => 'error', 'message' => 'Code is required'));
        }
        
        // Escape input to prevent SQL injection
        $code = $this->conn->real_escape_string($code);
        
        try {
            $sql = "SELECT * FROM establishment WHERE code = '$code'";
            $qry = $this->conn->query($sql);
            
            error_log("Establishment login query: " . $sql);
            
            if ($qry && $qry->num_rows > 0) {
                $establishment_data = $qry->fetch_array();
                foreach ($establishment_data as $k => $v) {
                    if (!is_numeric($k)) {
                        $this->settings->set_userdata($k, $v);
                    }
                }
                $this->settings->set_userdata('login_type', 2);
                
                error_log("Establishment login successful for code: " . $code);
                return json_encode(array('status' => 'success', 'redirect' => 'establishment/'));
            } else {
                error_log("Establishment login failed for code: " . $code);
                return json_encode(array('status' => 'incorrect', 'message' => 'Invalid code'));
            }
        } catch (Exception $e) {
            error_log("Establishment login error: " . $e->getMessage());
            return json_encode(array('status' => 'error', 'message' => 'Login failed due to system error'));
        }
    }
    
    public function rlogin()
    {
        // بررسی وجود POST data
        if (empty($_POST)) {
            return json_encode(array('status' => 'error', 'message' => 'No data received'));
        }
        
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        
        // Input validation
        if (empty($username)) {
            return json_encode(array('status' => 'error', 'message' => 'Username is required'));
        }
        
        // Escape input to prevent SQL injection
        $username = $this->conn->real_escape_string($username);
        
        try {
            // First try users table
            $sql = "SELECT * FROM users WHERE username = '$username'";
            $qry = $this->conn->query($sql);
            
            error_log("Remote login query (users): " . $sql);
            
            if ($qry && $qry->num_rows > 0) {
                $user_data = $qry->fetch_array();
                foreach ($user_data as $k => $v) {
                    if (!is_numeric($k)) {
                        $this->settings->set_userdata($k, $v);
                    }
                }
                $this->settings->set_userdata('login_type', 2);
                
                error_log("Remote login successful (users) for: " . $username);
                return json_encode(array('status' => 'success', 'redirect' => 'portal/'));
            } else {
                // Try event_audience table
                $sql = "SELECT * FROM event_audience WHERE contact = '$username'";
                $qry = $this->conn->query($sql);
                
                error_log("Remote login query (audience): " . $sql);
                
                if ($qry && $qry->num_rows > 0) {
                    $audience_data = $qry->fetch_array();
                    foreach ($audience_data as $k => $v) {
                        if (!is_numeric($k)) {
                            $this->settings->set_userdata($k, $v);
                        }
                    }
                    $this->settings->set_userdata('login_type', 2);
                    
                    error_log("Remote login successful (audience) for: " . $username);
                    return json_encode(array('status' => 'success', 'redirect' => 'portal/'));
                } else {
                    error_log("Remote login failed for: " . $username);
                    return json_encode(array('status' => 'incorrect', 'message' => 'User not found'));
                }
            }
        } catch (Exception $e) {
            error_log("Remote login error: " . $e->getMessage());
            return json_encode(array('status' => 'error', 'message' => 'Login failed due to system error'));
        }
    }
    
    public function logout()
    {
        if ($this->settings->sess_des()) {
            return json_encode(array('status' => 'success', 'redirect' => 'admin/login.php'));
        }
        return json_encode(array('status' => 'error', 'message' => 'Logout failed'));
    }
    
    public function rlogout()
    {
        if ($this->settings->sess_des()) {
            return json_encode(array('status' => 'success', 'redirect' => 'portal.php'));
        }
        return json_encode(array('status' => 'error', 'message' => 'Logout failed'));
    }
    
    public function elogout()
    {
        if ($this->settings->sess_des()) {
            return json_encode(array('status' => 'success', 'redirect' => 'portal.php'));
        }
        return json_encode(array('status' => 'error', 'message' => 'Logout failed'));
    }
    
    // متد کمکی برای تست اتصال
    public function test_connection()
    {
        if ($this->conn) {
            return json_encode(array('status' => 'success', 'message' => 'Database connected'));
        } else {
            return json_encode(array('status' => 'error', 'message' => 'Database connection failed'));
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
    case 'test':
        echo $auth->test_connection();
        break;
    default:
        echo $auth->index();
        break;
}
?>