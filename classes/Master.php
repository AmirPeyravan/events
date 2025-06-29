<?php
require_once('../config.php');
require_once('../libs/phpqrcode/qrlib.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function save_event(){
	    
		$data ="";https://iraninsurance.ir/%D8%AE%D8%AF%D9%85%D8%A7%D8%AA-%D8%A7%D9%84%DA%A9%D8%AA%D8%B1%D9%88%D9%86%DB%8C%DA%A9%DB%8C
		// $user_ids;
		
		foreach($_POST as $k =>$v){
			$_POST[$k] = addslashes($v);
		}
		extract($_POST);
		$check = $this->conn->query("SELECT * FROM event_list where title = '{$title}' ".($id > 0 ? " and id != '{$id}' " : ""))->num_rows;
		if($check > 0){
			$resp['status'] = "duplicate";
		}else{
            
             // Handle uploaded image
            if(isset($_FILES['image']) && $_FILES['image']['tmp_name'] != ''){
                $img_path = '../uploads/events_images/';
                if(!is_dir($img_path)) mkdir($img_path, 0777, true);
            
                $random_code = bin2hex(random_bytes(16)) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $target_file = $img_path . $random_code;
            
                if(move_uploaded_file($_FILES['image']['tmp_name'], $target_file)){
                    $_POST['image'] = $random_code; // Add file name to POST for DB
                } else {
                    $resp['status'] = 'failed';
                    $resp['error'] = 'Failed to upload image.';
                    return json_encode($resp);
                }
            }

			foreach($_POST as $k =>$v){
				if(!in_array($k,array('id'))){
					if(!empty($data)) $data .= ", ";
					if($k == 'user_id'){
					    $user_ids = '['. $v . ']';
					    $t= $v;
					    $data .= " `{$k}` = '{$user_ids}' ";
					}else {
					   $data .= " `{$k}` = '{$v}' ";
					}
				}
			}
        
            $user_ids_array = explode(",", $t);   

			if(empty($id)){
				$sql = "INSERT INTO event_list set $data";
			}else{
				$sql = "UPDATE event_list set $data where id = '{$id}'";
			}
			$save = $this->conn->query($sql);
            
            

			if($save){
				
				// Generate QR-Code 
				$code = empty($id) ? md5($this->conn->insert_id) : md5($id);
			    if(!is_dir('../temp/')) mkdir('../temp/');
			    $tempDir = '../temp/'; 
			    if(!is_file('../temp/'.$code.'.png'))
			    QRcode::png($code, $tempDir.''.$code.'.png', QR_ECLEVEL_L, 5);
			    $qr_link =  "https://event.noor-united.net/temp/".$code.'.png';
			 
			    // Update 
			    $event_id = empty($id) ? $this->conn->insert_id : $id;
			    // add to event_id 
                // foreach ($user_ids_array as $id) {
                //     $update_audience = "UPDATE event_audience SET event_id = '{$event_id}' WHERE id = $id";
                //     $this->conn->query($update_audience);
                // }
                
                $update = "UPDATE event_list set qr_link = '{$qr_link}' where id = '{$event_id}'";
			    $this->conn->query($update);
			    // Send Wahtsapp 
			    $this->send_whatsapp_message($qr_link,$user_ids_array, $event_id);
			    // Send Whatsapp   
			    $resp['status'] = 'success';
				$this->settings->set_flashdata("success", " Event Successfully Saved.");
			}else{
				$resp['status'] = 'failed';
				$resp['err'] = $this->conn->error;
				$resp['sql'] = $sql;
			}
		}


		return json_encode($resp);
	}
	function delete_event(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM event_list where id = '{$id}'");
		if($delete){
			$resp['status'] = "success";
			$this->settings->set_flashdata("success", " Event Successfully Deleted.");
		}else{
			$resp['status'] = "failed";
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
    
    function send_whatsapp_message($qr_link, $user_ids, $event_id) {
         

        if (empty($user_ids)) return;
    
        $event_sql = "SELECT * FROM event_list WHERE id = '{$event_id}'";
        $event_result = $this->conn->query($event_sql);
        if ($event_result->num_rows == 0) return;
        
        $event_hash = md5($event_id);
        
        $event = $event_result->fetch_assoc();
        $event_date = date("l, d F Y", strtotime($event['datetime_start']));
        $event_location ="Location: https://www.google.com/maps/search/?api=1&query={$event['venue']}";
        $event_show = "https://event.noor-united.net/admin/event/show.php?id={$event_hash}";
        // $decline_link ;
        
        $body = 
            "📅 *{$event_date}*\n" .
            "📍 *{$event_location}*\n\n" .
            
            "Please confirm your attendance by clicking one of the buttons below.\n\n".
            "✅ *Accept Invitation:* https://event.noor-united.net/portal.php\n" .
            "✅ [Show  Event Details ]({$event_show})\n";
            
            
    
        $fallback = $body . "\n\n📎 QR Invitation:\n" . $qr_link;
    
        $user_ids_sql = implode(",", array_map('intval', $user_ids));
        $sql = "SELECT * FROM event_audience WHERE id IN($user_ids_sql)";
        $qry = $this->conn->query($sql);
        

        while ($row = $qry->fetch_assoc()) {
            if (empty($row['contact'])) continue;
        
            $params = array(
                'token' => 'm14usuf9wweyssi8',
                'to' => $row['contact'],
                'body' => $fallback
            );
    
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/instance122212/messages/chat",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($params),
                CURLOPT_HTTPHEADER => array("Content-Type: application/x-www-form-urlencoded"),
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
            ));
    
            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                error_log("WhatsApp CURL Error: " . curl_error($curl));
            } else {
                error_log("WhatsApp response: " . $response);
            }
    
            curl_close($curl);
        }
    }


    // function send_whatsapp_message($qr_link, $user_ids, $event_id) {
    //     if (empty($user_ids)) return;
    
    //     // ✅ Fetch event info from event_list
    //     $event_sql = "SELECT * FROM event_list WHERE id = '{$event_id}'";
    //     $event_result = $this->conn->query($event_sql);
    
    //     if ($event_result->num_rows == 0) return;
    //     $event = $event_result->fetch_assoc();
    
    //     $event_date = date("l, d F Y", strtotime($event['datetime_start']));
    //     $event_location = $event['venue'];
    
    //     $body = 
    //         "📅 *{$event_date}*
    //         📍 *{$event_location}*
    
    //       Please confirm your attendance or apologize by clicking one of the buttons below.";
    
    //     $fallback = $body . "\n\n📎 QR Invitation:\n" . $qr_link;
        

    //     $sql = "SELECT * FROM users WHERE id IN(" . implode(",", array_map('intval', $user_ids)) . ")";
    //     $qry = $this->conn->query($sql);
    
    //     while ($row = $qry->fetch_assoc()) {
    //         $params = array(
    //             'token' => 'm14usuf9wweyssi8',
    //             'to' => $row['number'] ? $row['number'] : '' ,
    //             'body' => $fallback
    //         );
    
    //         $curl = curl_init();
    //         curl_setopt_array($curl, array(
    //             CURLOPT_URL => "https://api.ultramsg.com/instance122212/messages/chat",
    //             CURLOPT_RETURNTRANSFER => true,
    //             CURLOPT_POST => true,
    //             CURLOPT_POSTFIELDS => http_build_query($params),
    //             CURLOPT_HTTPHEADER => array("Content-Type: application/x-www-form-urlencoded"),
    //             CURLOPT_SSL_VERIFYHOST => 0,
    //             CURLOPT_SSL_VERIFYPEER => 0,
    //         ));
    
    //         $response = curl_exec($curl);
    //         if (curl_errno($curl)) {
    //             error_log("WhatsApp Error: " . curl_error($curl));
    //         }
    //         curl_close($curl);
    //     }
    // }

	
	
	function send_whatsapp_message_old($qr_link,$user_ids){
	    
	    $sql = "SELECT * FROM users WHERE id IN(";
	    $numItems = count($user_ids);
        $i = 0;
        
	    foreach ($user_ids as $value) {
	        if(++$i === $numItems) {
	            $sql= $sql . "'{$value}')";
	        }else{
	            $sql= $sql . "'{$value}' , ";
	        }
        }

        $qry=$this->conn->query($sql);
        
        $body ="you are invited, try to scan the qr-code";
        
        while($row= $qry->fetch_assoc()){
            $params=array(
                'token' => 'm14usuf9wweyssi8',
                'to' => $row['number'],
                'body' =>$body . "\n". $qr_link
            );
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://api.ultramsg.com/instance122212/messages/chat",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_SSL_VERIFYHOST => 0,
              CURLOPT_SSL_VERIFYPEER => 0,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => http_build_query($params),
              CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded"
              ),
            ));
        
            $response = curl_exec($curl);
            $err = curl_error($curl);
            
            curl_close($curl);
        }
    
	}
	
	public function save_audience() {
        $resp = ['status' => 'failed'];
        $skipped = [];
    
        // Sanitize all inputs
        foreach ($_POST as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $i => $val) {
                    $_POST[$k][$i] = addslashes($val);
                }
            } else {
                $_POST[$k] = addslashes($v);
            }
        }
    
        // Edit Mode
        if (!empty($_POST['id'])) {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $contact = $_POST['contact'];
    
            // Check for duplicate contact in other records
            $chk = $this->conn->query("SELECT id FROM event_audience WHERE contact = '{$contact}' AND id != '{$id}'");
            if ($chk->num_rows > 0) {
                $resp['status'] = 'skipped';
                $resp['message'] = 'Contact already exists for another audience.';
                return json_encode($resp);
            }
    
            $update = $this->conn->query("UPDATE event_audience SET name = '{$name}', contact = '{$contact}' WHERE id = '{$id}'");
            if ($update) {
                $resp['status'] = 'success';
                $this->settings->set_flashdata("success", "Audience updated successfully.");
            } else {
                $resp['status'] = 'failed';
                $resp['error'] = $this->conn->error;
            }
        } 
        // Add Mode (multiple)
        else {
            $names = $_POST['name'];
            $contacts = $_POST['contact'];
            $owner = $_POST['owner'];
            for ($i = 0; $i < count($names); $i++) {
                $name = $names[$i];
                $contact = $contacts[$i];
    
                // Check if contact already exists
                $check = $this->conn->query("SELECT id FROM event_audience WHERE contact = '{$contact}'");
                if ($check && $check->num_rows > 0) {
                    $skipped[] = $contact;
                    continue;
                }
    
                // Insert if not duplicate
                $sql = "INSERT INTO event_audience (`name`, `contact` , `owner`) VALUES ('{$name}', '{$contact}' , '{$owner}')";
                $save = $this->conn->query($sql);
    
                if (!$save) {
                    $resp['status'] = 'failed';
                    $resp['error'] = $this->conn->error;
                    $resp['sql'] = $sql;
                    return json_encode($resp);
                }
            }
    
            if (!empty($skipped)) {
                $resp['status'] = 'skipped';
                $resp['message'] = "Some contacts were skipped: " . implode(', ', $skipped);
                $this->settings->set_flashdata("warning", $resp['message']);
            } else {
                $resp['status'] = 'success';
                $this->settings->set_flashdata("success", "All audiences successfully saved.");
            }
        }
    
        return json_encode($resp);
    }


// 	public function save_audience_new1() {
//         $resp = ['status' => 'failed'];
        
//         // Sanitize all inputs
//         foreach ($_POST as $k => $v) {
//             if (is_array($v)) {
//                 foreach ($v as $i => $val) {
//                     $_POST[$k][$i] = addslashes($val);
//                 }
//             } else {
//                 $_POST[$k] = addslashes($v);
//             }
//         }
    
//         // Check if arrays exist
//         if (isset($_POST['name']) && isset($_POST['contact'])) {
//             $names = $_POST['name'];
//             $contacts = $_POST['contact'];
    
//             for ($i = 0; $i < count($names); $i++) {
//                 $name = $names[$i];
//                 $contact = $contacts[$i];
    
//                 $sql = "INSERT INTO event_audience (`name`, `contact`) VALUES ('{$name}', '{$contact}')";
//                 $save = $this->conn->query($sql);
    
//                 if (!$save) {
//                     $resp['status'] = 'failed';
//                     $resp['error'] = $this->conn->error;
//                     $resp['sql'] = $sql;
//                     return json_encode($resp); // Return early if any insert fails
//                 }
//             }
    
//             $resp['status'] = 'success';
//             $this->settings->set_flashdata("success", "Audience Successfully Saved.");
//         }
    
//         return json_encode($resp);
//     }
    public function save_audience_new2() {
        $resp = ['status' => 'failed'];
        $skipped = [];
        
        // Sanitize all inputs
        foreach ($_POST as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $i => $val) {
                    $_POST[$k][$i] = addslashes($val);
                }
            } else {
                $_POST[$k] = addslashes($v);
            }
        }
    
        if (isset($_POST['name']) && isset($_POST['contact'])) {
            $names = $_POST['name'];
            $contacts = $_POST['contact'];
    
            for ($i = 0; $i < count($names); $i++) {
                $name = $names[$i];
                $contact = $contacts[$i];
    
                // Check if contact already exists
                $check = $this->conn->query("SELECT id FROM event_audience WHERE contact = '{$contact}'");
                if ($check && $check->num_rows > 0) {
                    $skipped[] = $contact;
                    continue;
                }
    
                // Insert if not duplicate
                $sql = "INSERT INTO event_audience (`name`, `contact`) VALUES ('{$name}', '{$contact}')";
                $save =$this->conn->query($sql);
                
                if (!$save) {
                    $resp['status'] = 'failed';
                    $resp['error'] = $this->conn->error;
                    $resp['sql'] = $sql;
                    return json_encode($resp);
                }
            }
    
            
            if (!empty($skipped)) { 
                $this->settings->set_flashdata("warning", "Some contacts were skipped: " . implode(', ', $skipped));
                $resp['status'] = 'skipped';
            } else {
                $resp['status'] = 'success';
                $this->settings->set_flashdata("success", "All Audiences Successfully Saved.");
            }
            
            
        }
    
        return json_encode($resp);
    }
	public function save_audience_old(){
		$data ="";
		foreach($_POST as $k =>$v){
			$_POST[$k] = addslashes($v);
		}
		extract($_POST);

		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .= ", ";
				$data .= " `{$k}` = '{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO event_audience set $data";
		}else{
			$sql = "UPDATE event_audience set $data where id = '{$id}'";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			$code = empty($id) ? md5($this->conn->insert_id) : md5($id);
			if(!is_dir('../temp/')) mkdir('../temp/');
			$tempDir = '../temp/'; 
			if(!is_file('../temp/'.$code.'.png'))
			QRcode::png($code, $tempDir.''.$code.'.png', QR_ECLEVEL_L, 5);
			$this->settings->set_flashdata("success", " Event Guest Successfully Saved.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error;
			$resp['sql'] = $sql;
		}

		
		return json_encode($resp);
	}
	function delete_audience(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM event_audience where id = '{$id}'");
		if($delete){
			$resp['status'] = "success";
			$this->settings->set_flashdata("success", " Event Guest Successfully Deleted.");
		}else{
			$resp['status'] = "failed";
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function load_registration(){
		extract($_POST);

// 		$qry = $this->conn->query("SELECT a.*,r.id as rid,r.date_created as rdate FROM registration_history r inner join users a on a.id =r.user_id where r.event_id = '{$event_id}' and r.id > '{$last_id}' order by r.id asc ");
		$qry = $this->conn->query("SELECT a.*,r.id as rid,r.date_created as rdate FROM registration_history r inner join event_audience a on a.id =r.user_id where r.event_id = '{$event_id}' and r.id > '{$last_id}' order by r.id asc ");

		
		$data=array();
		while($row=$qry->fetch_assoc()){
			$row['rdate'] = date("M d, Y h:i A",strtotime($row['rdate']));
			$data[]=$row;
		}
		return json_encode($data);
	}
	function register(){
		extract($_POST);
		$query = $this->conn->query("SELECT * FROM event_list where md5(id) = '{$audience_id}' and md5(id)='{$event_id}' ");
        
		if($query->num_rows > 0){
			$res = $query->fetch_assoc();
			
			$check = $this->conn->query("SELECT * from registration_history where event_id = '{$res['id']}' and  user_id = '{$this->settings->userdata('id')}' ");
			if($check->num_rows > 0){
				$resp['status']=3;
				$resp['name']=$res['title'];
			}else{

				$insert = $this->conn->query("INSERT INTO registration_history set event_id = '{$res['id']}',  audience_id = '{$res['id']}',`user_id` = '{$this->settings->userdata('id')}'  ");
				if($insert){
					$resp['status']=1;
					$resp['name']=$res['title'];
				}else{
					$resp['status']=2;
					$resp['error']=$this->conn->error;
				}
			}

		}else{
			$resp['status']=2;
		}
		return json_encode($resp);
	}
}

$main = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
	case 'save_event':
		echo $main->save_event();
	break;
	case 'delete_event':
		echo $main->delete_event();
	break;
	case 'save_audience':
		echo $main->save_audience();
	break;
	case 'delete_audience':
		echo $main->delete_audience();
	break;
	case 'load_registration':
		echo $main->load_registration();
	break;
	case 'register':
		echo $main->register();
	break;
	default:
		// echo $sysset->index();
		break;
}