<?php 
require_once('../../config.php');
// Show errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if(isset($_GET['id']) && !empty($_GET['id'])){
    $hash = $_GET['id'];
    $hash_safe = $conn->real_escape_string($hash);

    // Search using MD5(id)
    $qry = $conn->query("SELECT * FROM event_list WHERE MD5(id) = '$hash_safe'");;
    
    if ($qry->num_rows == 0) {
        echo "Event not found.";
        exit;
    }
        
    $event = $qry->fetch_assoc();
    $event_title = ucwords($event['title']);
    $event_description = $event['description'];
    $datetime_start = date("l، d F Y h:i A", strtotime($event['datetime_start']));
    $datetime_end = date("l، d F Y h:i A", strtotime($event['datetime_end']));
    $event_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $qr_code_url = $event['qr_link'];
    $image= $event['image'];
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Invitation</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: url('<?php echo "https://event.noor-united.net/uploads/events_images/{$image}"; ?>') no-repeat center center fixed;
            background-size: cover;
            text-align: center;
            padding: 30px;
        }
        .card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            max-width: 600px;
            margin: auto;
            background: url('<?php echo "https://event.noor-united.net/uploads/events_images/{$image}"; ?>') no-repeat center center fixed;
            background-size: cover;
        }
        h2 {
            margin-bottom: 10px;
        }
        .qr-code {
            margin-top: 20px;
        }
        .details {
            margin-top: 20px;
            font-size: 16px;
            line-height: 2;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2><?php echo $event_title; ?></h2>
        <p><?php echo nl2br($event_description ?? ''); ?></p>

        <div class="details">
            <p><strong>Start:</strong> <?php echo $datetime_start; ?></p>
            <p><strong>End:</strong> <?php echo $datetime_end; ?></p>
        </div>

        <div class="qr-code">
            <img src="<?php echo $qr_code_url; ?>" alt="QR Code">
            <p>Scan the QR code for event details</p>
        </div>

        <p style="margin-top: 30px; font-style: italic;">Your presence completes our joy</p>
        <p><small>Personal invitation only</small></p>
    </div>
</body>
</html>