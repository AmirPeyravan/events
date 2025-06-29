<?php
// event_response.php
require_once __DIR__ . '/../../config.php';

if (isset($_GET['action']) && isset($_GET['aid'])) {
    $action = $_GET['action'];
    $audience_id = $_GET['aid'];
    
    // بررسی معتبر بودن audience_id
    $check_query = "SELECT * FROM event_audience WHERE id = '$audience_id'";
    $check_result = $conn->query($check_query);
    
    if ($check_result->num_rows > 0) {
        $audience = $check_result->fetch_assoc();
        
        if ($action == 'accept') {
            // قبول کردن - status = 1
            $update_query = "UPDATE event_audience SET status = 1 WHERE id = '$audience_id'";
            $conn->query($update_query);
            
            $message = "✅ شما این رویداد را قبول کردید.";
            $status = "قبول شده";
            
        } elseif ($action == 'reject') {
            // رد کردن - status = 0
            $update_query = "UPDATE event_audience SET status = 0 WHERE id = '$audience_id'";
            $conn->query($update_query);
            
            $message = "❌ شما این رویداد را رد کردید.";
            $status = "رد شده";
        }
        
        // دریافت اطلاعات رویداد برای نمایش
        $event_id = $audience['event_id'];
        $event_query = "SELECT * FROM event_list WHERE id = '$event_id'";
        $event_result = $conn->query($event_query);
        $event = $event_result->fetch_assoc();
        
        ?>
        <!DOCTYPE html>
        <html lang="fa" dir="rtl">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>پاسخ به دعوت</title>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header text-center <?php echo $action == 'accept' ? 'bg-success' : 'bg-danger'; ?> text-white">
                                <h4><?php echo $message; ?></h4>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $event['title']; ?></h5>
                                <p class="card-text"><?php echo $event['description']; ?></p>
                                <hr>
                                <p><strong>تاریخ شروع:</strong> <?php echo date('Y/m/d H:i', strtotime($event['datetime_start'])); ?></p>
                                <p><strong>تاریخ پایان:</strong> <?php echo date('Y/m/d H:i', strtotime($event['datetime_end'])); ?></p>
                                <p><strong>وضعیت شما:</strong> <span class="badge <?php echo $action == 'accept' ? 'badge-success' : 'badge-danger'; ?>"><?php echo $status; ?></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        
    } else {
        echo "لینک نامعتبر است.";
    }
} else {
    echo "پارامترهای نامعتبر.";
}
?>