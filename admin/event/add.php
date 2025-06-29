<?php
// نمایش خطاها برای خطایابی دقیق
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// اتصال به تنظیمات و دیتابیس
require_once __DIR__ . '/../../config.php';

// ذخیره فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $venue = $conn->real_escape_string($_POST['venue']);
    $description = $conn->real_escape_string($_POST['description']);
    $datetime_start = $conn->real_escape_string($_POST['datetime_start']);
    $datetime_end = $conn->real_escape_string($_POST['datetime_end']);
    $user_id = isset($_POST['customer']['user_id'][0]) ? intval($_POST['customer']['user_id'][0]) : 0;
    $limit_registration = isset($_POST['limit_registration']) ? 1 : 0;
    $limit_time = isset($_POST['limit_time']) ? floatval($_POST['limit_time']) : NULL;

    // افزودن اطلاعات به جدول event_list
    $stmt = $conn->prepare("INSERT INTO event_list 
        (title, venue, description, datetime_start, datetime_end, user_id, limit_registration, limit_time, date_created, date_update) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");

    $stmt->bind_param("sssssiid", $title, $venue, $description, $datetime_start, $datetime_end, $user_id, $limit_registration, $limit_time);

    if ($stmt->execute()) {
        $_settings->set_flashdata('success', 'Event successfully added!');
        header('Location: ../?page=event');
        exit;
    } else {
        echo "<div class='alert alert-danger'>خطا در ثبت اطلاعات: " . $stmt->error . "</div>";
    }
}
?>

<div class="col-lg-12">
  <div class="card card-outline card-primary">
    <div class="card-header">
      <h3 class="card-title">Add Event</h3>
    </div>
    <div class="card-body">
      <form action="" method="post" id="event-frm">
        <div class="form-group">
          <label for="title" class="control-label">Title</label>
          <input type="text" class="form-control form-control-sm" name="title" id="title" required>
        </div>

        <div class="form-group">
          <label for="venue" class="control-label">Location (concat address with +)</label>
          <input type="text" placeholder="Hyatt+Regency+Kuwait" class="form-control form-control-sm" name="venue" id="venue" required>
        </div>

        <div class="form-group">
          <label for="description" class="control-label">Description</label>
          <textarea class="form-control form-control-sm" name="description" id="description" required></textarea>
        </div>

        <div class="form-group">
          <label for="datetime_start" class="control-label">DateTime Start</label>
          <input type="datetime-local" class="form-control form-control-sm" name="datetime_start" id="datetime_start" required>
        </div>

        <div class="form-group">
          <label for="datetime_end" class="control-label">DateTime End</label>
          <input type="datetime-local" class="form-control form-control-sm" name="datetime_end" id="datetime_end" required>
        </div>

        <?php
        $user_id = 0;
        if ($_settings->userdata('login_type') == 'event_manager') {
            $owner = $_settings->userdata('id');
            $sql = "SELECT id, concat(name,' ',contact) as name FROM event_audience WHERE owner = $owner ORDER BY name ASC";
        } else if ($_settings->userdata('login_type') == 1) {
            $sql = "SELECT id, concat(name,' ',contact) as name FROM event_audience ORDER BY name ASC";
        }

        $qry = $conn->query($sql);
        if ($qry && $qry->num_rows > 0):
        ?>
        <div class="form-group">
          <label for="user_id" class="control-label">Assign To</label>
          <select name="customer[user_id][]" id="customer_user_id_" class="custom-select select2" required multiple>
            <option></option>
            <?php while ($row = $qry->fetch_assoc()): ?>
              <option value="<?php echo $row['id'] ?>"><?php echo ucwords($row['name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <?php endif; ?>

        <div class="form-group">
          <div class="icheck-primary">
            <input type="checkbox" id="limit_registration" name="limit_registration" value="1">
            <label for="limit_registration">Limited Time Of Registration Only</label>
          </div>
        </div>

        <div class="form-group" id="limit_time_group" style="display: none;">
          <label for="limit_time" class="control-label">Limit Registration Time (In Minutes)</label>
          <input type="number" min="0" class="form-control form-control-sm" name="limit_time" id="limit_time">
        </div>

        <div class="text-right">
          <button class="btn btn-primary">Save Event</button>
          <a href="../?page=event" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // نمایش فیلد زمان ثبت محدود در صورت انتخاب چک‌باکس
  document.getElementById('limit_registration').addEventListener('change', function () {
    document.getElementById('limit_time_group').style.display = this.checked ? 'block' : 'none';
  });
</script>
