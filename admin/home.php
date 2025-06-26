<h1>Welcome to <?php echo $_settings->info('name') ?></h1>
<hr>
<section class="content">
<div class="row">
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-th-list"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Events</span>
                <span class="info-box-number">
                  <?php echo $conn->query("SELECT * FROM event_list")->num_rows; ?>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Listed Audience</span>
                <span class="info-box-number"> <?php echo $conn->query("SELECT * FROM event_audience")->num_rows; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>

          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-th-list"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Finished Event</span>
                <span class="info-box-number"><?php echo $conn->query("SELECT * FROM event_list where unix_timestamp(datetime_end) <= '".strtotime(date('Y-m-d H:i'))."' ")->num_rows; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-th-list"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">On-Going Events</span>
                <span class="info-box-number"><?php echo $conn->query("SELECT * FROM event_list where '".strtotime(date('Y-m-d H:i'))."' between unix_timestamp(datetime_start) and unix_timestamp(datetime_end) ")->num_rows; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
        </div>
</section>