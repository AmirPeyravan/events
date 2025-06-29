<?php
// بررسی وجود session و تنظیمات
if (!isset($_settings)) {
    die("Settings not initialized");
}

// نمایش پیام موفقیت
if ($_settings->chk_flashdata('success')): ?>
    <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
    </script>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap4.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
</head>

<body>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Event Management</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Events</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Event List</h3>

                                <?php
                                // تعریف و اجرای Query بر اساس نوع کاربر
                                $sql = "";
                                $login_type = $_settings->userdata('login_type');

                                try {
                                    if ($login_type == 1) {
                                        // Admin - نمایش همه رویدادها
                                        $sql = "SELECT * FROM event_list ORDER BY title ASC";
                                    } elseif ($login_type == 'event_manager') {
                                        // Event Manager - فقط رویدادهای خودش
                                        $owner = $_settings->userdata('id');
                                        $sql = "SELECT * FROM event_list WHERE owner = '$owner' ORDER BY title ASC";
                                    } else {
                                        // سایر کاربران - همه رویدادها
                                        $sql = "SELECT * FROM event_list ORDER BY title ASC";
                                    }

                                    $qry = $conn->query($sql);

                                    if (!$qry) {
                                        throw new Exception("Query Error: " . $conn->error);
                                    }
                                } catch (Exception $e) {
                                    echo "<div class='alert alert-danger'>Database Error: " . $e->getMessage() . "</div>";
                                    $qry = false;
                                }
                                ?>

                                <div class="card-tools">
                                    <?php
                                    // نمایش دکمه Add New برای Admin و Event Manager
                                    if ($login_type == 1 || $login_type == 'event_manager'): ?>
                                        <a class="btn btn-sm btn-primary new_event" href="http://localhost:5000/admin/?page=event/add">
                                            <i class="fa fa-plus">Add New Event</i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="card-body">
                                <?php if ($qry && $qry->num_rows > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered table-striped" id="eventTable">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th class="text-center" width="5%">#</th>
                                                    <th width="20%">Title</th>
                                                    <th width="25%">Description</th>
                                                    <th width="25%">Details</th>
                                                    <th class="text-center" width="10%">Status</th>
                                                    <th class="text-center" width="15%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;

                                                // دریافت لیست کاربران برای نمایش assignee
                                                $users_query = "SELECT id, CONCAT(firstname, ' ', lastname) as name FROM users WHERE `type` = 2";
                                                $users = $conn->query($users_query);
                                                $assignees = array();

                                                if ($users) {
                                                    while ($urow = $users->fetch_assoc()) {
                                                        $assignees[$urow['id']] = ucwords($urow['name']);
                                                    }
                                                }

                                                // نمایش رویدادها
                                                while ($row = $qry->fetch_assoc()):
                                                    $assignee = isset($assignees[$row['user_id']]) ? $assignees[$row['user_id']] : "N/A";

                                                    // تعیین وضعیت رویداد
                                                    $current_time = time();
                                                    $start_time = strtotime($row['datetime_start']);
                                                    $end_time = strtotime($row['datetime_end']);

                                                    if ($start_time > $current_time) {
                                                        $status = '<span class="badge badge-secondary">Pending</span>';
                                                    } elseif ($end_time <= $current_time) {
                                                        $status = '<span class="badge badge-success">Completed</span>';
                                                    } else {
                                                        $status = '<span class="badge badge-primary">On-Going</span>';
                                                    }
                                                ?>
                                                    <tr>
                                                        <td class="text-center"><?php echo $i++ ?></td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars(ucwords($row['title'])) ?></strong>
                                                            <?php if (isset($row['featured']) && $row['featured'] == 1): ?>
                                                                <span class="badge badge-warning badge-sm">Featured</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <div class="description-container">
                                                                <?php echo htmlspecialchars($row['description']) ?>
                                                                <div class="mt-1">
                                                                    <a href="javascript:void(0)" class="btn btn-outline-info btn-xs view_data" data-id="<?php echo $row['id'] ?>" title="View QR Code">
                                                                        <i class="fa fa-qrcode"></i> QR
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="event-details">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-play text-success"></i>
                                                                    <strong>Start:</strong> <?php echo date("M d, Y h:i A", $start_time) ?>
                                                                </small><br>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-stop text-danger"></i>
                                                                    <strong>End:</strong> <?php echo date("M d, Y h:i A", $end_time) ?>
                                                                </small><br>
                                                                <?php if ($assignee != "N/A"): ?>
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-user"></i>
                                                                        <strong>Assignee:</strong> <?php echo $assignee ?>
                                                                    </small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php echo $status ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group" role="group">

                                                                <a href="<?php echo 'http://localhost:5000/admin/event/send_whatsapp.php?user_id=' . $row['user_id']; ?>"
                                                                    onclick="return confirmSendWhatsApp();"
                                                                    class="btn btn-sm btn-success"
                                                                    title="ارسال دعوت در واتساپ">
                                                                    <i class="fab fa-whatsapp"></i>
                                                                </a>

                                                                <a href="javascript:void(0)"
                                                                    data-id='<?php echo $row['id'] ?>'
                                                                    class="btn btn-sm btn-warning manage_event"
                                                                    title="Edit Event">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="event/show.php?id=<?php echo md5((int)$row['id']); ?>"
                                                                    class="btn btn-sm btn-info"
                                                                    target="_blank"
                                                                    title="View Event">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger delete_event"
                                                                    data-id="<?php echo $row['id'] ?>"
                                                                    title="Delete Event">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>

                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                                        <h5>No Events Found</h5>
                                        <p>There are currently no events to display.</p>
                                        <?php if ($login_type == 1 || $login_type == 'event_manager'): ?>
                                            <a class="btn btn-primary new_event" href="javascript:void(0)">
                                                <i class="fa fa-plus"></i> Create Your First Event
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($qry && $qry->num_rows > 0): ?>
                                <div class="card-footer">
                                    <small class="text-muted">
                                        Total Events: <strong><?php echo $qry->num_rows ?></strong> |
                                        Last Updated: <strong><?php echo date('M d, Y h:i A') ?></strong>
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>

    <style>
        .description-container {
            max-width: 250px;
        }

        .event-details small {
            display: block;
            margin-bottom: 2px;
        }

        .btn-group .btn {
            margin: 0 1px;
        }

        .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.75em;
        }

        .card-header .card-tools {
            margin: 0;
        }

        @media (max-width: 768px) {
            .btn-group {
                display: flex;
                flex-direction: column;
            }

            .btn-group .btn {
                margin-bottom: 2px;
                border-radius: 4px !important;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#eventTable').DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "ordering": true,
                "info": true,
                "paging": true,
                "searching": true,
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "language": {
                    "search": "Search Events:",
                    "lengthMenu": "Show _MENU_ events per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ events",
                    "infoEmpty": "No events available",
                    "infoFiltered": "(filtered from _MAX_ total events)",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                },
                "columnDefs": [{
                        "orderable": false,
                        "targets": [5]
                    } // Disable ordering on Actions column
                ],
                "order": [
                    [1, "asc"]
                ] // Default sort by Title
            });

            // Add New Event
            //$('.new_event').click(function(e) {
            //  e.preventDefault();
            //uni_modal("New Event", "./event/manage.php", "large");
            //});

            // Edit Event
            $(document).on('click', '.manage_event', function(e) {
                e.preventDefault();
                var eventId = $(this).attr('data-id');
                uni_modal("Edit Event", "./event/manage.php?id=" + eventId, "large");
            });

            // View QR Code
            $(document).on('click', '.view_data', function(e) {
                e.preventDefault();
                var eventId = $(this).attr('data-id');
                uni_modal("Event QR Code", "./event/view.php?id=" + eventId, "medium");
            });

            // Delete Event
            $(document).on('click', '.delete_event', function(e) {
                e.preventDefault();
                var eventId = $(this).attr('data-id');
                var eventTitle = $(this).closest('tr').find('td:nth-child(2) strong').text();

                _conf("Are you sure you want to delete the event '" + eventTitle + "'?<br><small class='text-danger'>This action cannot be undone.</small>", "delete_event", [eventId]);
            });

            // Refresh page after modal close
            $(document).on('hidden.bs.modal', '.modal', function() {
                if ($(this).find('.modal-title').text().includes('Event')) {
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                }
            });
        });

        // Delete Event Function
        function delete_event(eventId) {
            start_loader();

            $.ajax({
                url: _base_url_ + 'classes/Master.php?f=delete_event',
                method: 'POST',
                data: {
                    id: eventId
                },
                dataType: "json",
                beforeSend: function() {
                    // Disable delete button to prevent double click
                    $('.delete_event[data-id="' + eventId + '"]').prop('disabled', true);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert_toast("An error occurred while deleting the event. Please try again.", 'error');
                    end_loader();
                    $('.delete_event[data-id="' + eventId + '"]').prop('disabled', false);
                },
                success: function(resp) {
                    if (resp.status == "success") {
                        alert_toast("Event deleted successfully!", 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        alert_toast("Failed to delete event: " + (resp.message || "Unknown error"), 'error');
                        $('.delete_event[data-id="' + eventId + '"]').prop('disabled', false);
                    }
                    end_loader();
                }
            });
        }

        // Auto-refresh page every 5 minutes to show updated event statuses
        setInterval(function() {
            if (!$('.modal').hasClass('show')) { // Only refresh if no modal is open
                location.reload();
            }
        }, 300000); // 5 minutes

        // Show loading spinner for AJAX requests
        $(document).ajaxStart(function() {
            start_loader();
        }).ajaxStop(function() {
            end_loader();
        });




        var _base_url_ = "http://localhost:5000"; // مسیر پروژه شما

        $(document).on('click', '.send_whatsapp', function(e) {
            e.preventDefault();
            var eventId = $(this).attr('data-id');

            if (confirm("آیا مطمئنید که می‌خواهید دعوت واتساپ ارسال شود؟")) {
                start_loader();

                $.ajax({
                    url: _base_url_ + '/admin/event/send_whatsapp.php',
                    method: 'POST',
                    data: {
                        id: eventId
                    },
                    dataType: 'json',
                    success: function(resp) {
                        end_loader();
                        console.log('Response:', resp);
                        if (resp.status === 'success') {
                            alert_toast(resp.message, 'success');
                        } else {
                            alert_toast(resp.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        end_loader();
                        console.error('خطای Ajax:', xhr.responseText);
                        alert_toast("خطا در ارتباط با سرور", 'error');
                    }
                });
            }
        });
    </script>
</body>

</html>