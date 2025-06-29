<?php
// send_whatsapp.php
require_once __DIR__ . '/../../config.php';

// فعال‌سازی گزارش خطا
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// تشخیص متد و دریافت ID
$event_id = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $event_id = $_POST['id'];
    error_log("✅ Event ID دریافت شد از POST: $event_id");
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $event_id = $_GET['id'];
    error_log("✅ Event ID دریافت شد از GET: $event_id");
} else {
    error_log("❌ هیچ شناسه‌ای از سمت کلاینت ارسال نشد.");
    echo json_encode(['status' => 'error', 'message' => 'شناسه رویداد ارسال نشده']);
    exit;
}

// دریافت اطلاعات رویداد
$event_query = "SELECT * FROM event_list WHERE id = '$event_id'";
$event_result = $conn->query($event_query);

if (!$event_result) {
    error_log("❌ خطا در اجرای کوئری event_list: " . $conn->error);
    echo json_encode(['status' => 'error', 'message' => 'خطا در دریافت اطلاعات رویداد']);
    exit;
}

if ($event_result->num_rows === 0) {
    error_log("❌ هیچ رویدادی با این ID پیدا نشد.");
    echo json_encode(['status' => 'error', 'message' => 'رویداد پیدا نشد']);
    exit;
}

$event = $event_result->fetch_assoc();
error_log("✅ رویداد دریافت شد: " . $event['title']);

// دریافت لیست مخاطبان
$audience_query = "SELECT * FROM event_audience WHERE event_id = '$event_id'";
$audience_result = $conn->query($audience_query);

if (!$audience_result) {
    error_log("❌ خطا در اجرای کوئری event_audience: " . $conn->error);
    echo json_encode(['status' => 'error', 'message' => 'خطا در دریافت مخاطبان']);
    exit;
}

error_log("👥 تعداد مخاطبان: " . $audience_result->num_rows);

$sent_count = 0;
$failed_count = 0;

// حلقه ارسال پیام
while ($audience = $audience_result->fetch_assoc()) {
    $phone = trim($audience['remarks']);
    $audience_id = $audience['id'];

    error_log("📞 بررسی شماره مخاطب: $phone");

    if (empty($phone)) {
        error_log("⚠️ شماره مخاطب خالی است.");
        continue;
    }

    if (!preg_match('/^\+98\d{9,10}$/', $phone)) {
        error_log("❌ شماره نامعتبر: $phone");
        $failed_count++;
        continue;
    }

    // تنظیم پیام و لینک‌ها
    $instance = "instance128866";
    $token = "4skgkbr4z0fcehf9";

    $accept_link = "http://localhost:5000/event_response.php?action=accept&aid=$audience_id";
    $reject_link = "http://localhost:5000/event_response.php?action=reject&aid=$audience_id";

    $message = "🎉 دعوت به رویداد: {$event['title']}\n\n";
    $message .= "📅 شروع: " . date('Y/m/d H:i', strtotime($event['datetime_start'])) . "\n";
    $message .= "📅 پایان: " . date('Y/m/d H:i', strtotime($event['datetime_end'])) . "\n\n";
    $message .= "📝 توضیحات: {$event['description']}\n\n";
    $message .= "لطفاً پاسخ دهید:\n\n";
    $message .= "✅ قبول: $accept_link\n";
    $message .= "❌ رد: $reject_link";

    // ارسال به API
    $url = "https://api.ultramsg.com/$instance/messages/chat";
    $data = [
        'token' => $token,
        'to' => $phone,
        'body' => $message,
        'priority' => 1
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error_msg = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        error_log("❌ CURL Error for $phone: $error_msg");
        $failed_count++;
        continue;
    }

    error_log("📤 پاسخ API برای $phone: HTTP $http_code - $response");

    if ($http_code === 200 && str_contains($response, '"sent":"true"')) {
        $sent_count++;
    } else {
        $failed_count++;
    }
}

// پاسخ نهایی به کلاینت
if ($sent_count > 0) {
    echo json_encode([
        'status' => 'success',
        'message' => "پیام به $sent_count نفر ارسال شد" . ($failed_count > 0 ? " - $failed_count ناموفق" : "")
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'هیچ پیامی ارسال نشد'
    ]);
}
