<?php
require_once('config.php');

if(isset($_GET['e']) && !empty($_GET['e']) && isset($_GET['u']) && !empty($_GET['u'])){
	$qry = $conn->query("SELECT * FROM event_list WHERE md5(id) = '{$_GET['e']}'");
	foreach($qry->fetch_array() as $k => $v){
		if(!is_numeric($k)){
			$$k = $v;
		}
	}
	$user_id = intval($_GET['u']);

	// Update or insert decline status
	$check = $conn->query("SELECT * FROM event_attendance WHERE event_id = $id AND user_id = $user_id");
	if ($check->num_rows > 0) {
		$conn->query("UPDATE event_attendance SET status = 'declined' WHERE event_id = $id AND user_id = $user_id");
	} else {
		$conn->query("INSERT INTO event_attendance (event_id, user_id, status) VALUES ($id, $user_id, 'declined')");
	}

	// Optional: Get user name
	$user_q = $conn->query("SELECT firstname, lastname FROM users WHERE id = $user_id");
	$user = $user_q->fetch_assoc();
	$fullname = $user ? $user['firstname'] . ' ' . $user['lastname'] : "User";
}
?>

<style>
    body {
        background-image: url('bg.png');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #5a4a3c;
    }
    .content-wrapper {
        background: rgba(255, 255, 255, 0.85);
        margin: 3em auto;
        padding: 2em;
        border-radius: 12px;
        max-width: 800px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    h3.text-danger {
        color: #b22222;
        text-align: center;
        font-weight: bold;
    }
    dl dt {
        font-weight: bold;
    }
    .undo-btn {
        display: inline-block;
        padding: 0.5em 1.2em;
        background: #999;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        margin-top: 1em;
    }
    .undo-btn:hover {
        background: #666;
    }
</style>

<div class="content-wrapper">
    <h3 class="text-danger">Invitation Declined</h3>
    <div class="row">
        <div class="col-md-6">
            <dl>
                <dt>Event Title</dt>
                <dd><?php echo $title ?></dd>
            </dl>
            <dl>
                <dt>Event Location</dt>
                <dd><?php echo $venue ?></dd>
            </dl>
            <dl>
                <dt>Event Description</dt>
                <dd><?php echo $description ?></dd>
            </dl>
        </div>
        <div class="col-md-6">
            <dl>
                <dt>Event Start</dt>
                <dd><?php echo date("M d, Y h:i A",strtotime($datetime_start)) ?></dd>
            </dl>
            <dl>
                <dt>Event End</dt>
                <dd><?php echo date("M d, Y h:i A",strtotime($datetime_end)) ?></dd>
            </dl>
            <?php if($limit_registration == 1): ?>
            <dl>
                <dt>Registration Cut-off Time</dt>
                <dd><?php echo date("M d, Y h:i A",strtotime($datetime_end.' + '.$limit_time.' minutes')) ?></dd>
            </dl>
            <?php endif; ?>
        </div>
    </div>

    <hr>
    <div class="text-center">
        <h5><?php echo $fullname ?>, you have successfully declined your attendance to this event.</h5>
        <a class="undo-btn" href="undo_decline.php?e=<?php echo $_GET['e'] ?>&u=<?php echo $user_id ?>">Undo</a>
    </div>
</div>
