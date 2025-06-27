<?php require_once('config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<?php require_once('inc/header.php') ?>

<body class="hold-transition lockscreen">
  <script>
    start_loader()
  </script>
  <style>
    .lockscreen-image {
      left: -27px;
      top: -3px;
    }
  </style>
  <div class="lockscreen-wrapper">
    <div class="lockscreen-logo">
      <a href="<?php echo base_url ?>"><strong><?php echo $_settings->info('name') ?></strong></a>
    </div>
    <!-- User name -->
    <div class="lockscreen-name"></div>

    <!-- START LOCK SCREEN ITEM -->
    <div class="lockscreen-item">

      <div class="lockscreen-image">
        <img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="System Image">
      </div>

      <form class="lockscreen-credentials" id="r-login">
        <div class="input-group">
          <input type="text" class="form-control" name="username" placeholder="Username OR Mobile">

          <div class="input-group-append">
            <button type="submit" class="btn">
              <i class="fas fa-arrow-right text-muted"></i>
            </button>
          </div>
        </div>

        <div class="input-group">
          <input type="password" class="form-control" name="password" placeholder="Password">

          <div class="input-group-append">
            <button type="submit" class="btn">
              <i class="fas fa-arrow-right text-muted"></i>
            </button>
          </div>
        </div>
      </form>

    </div>

    <div id="msg"></div>
    <div class="help-block text-center">
      <a href="<?php echo base_url . 'admin' ?>">Login as Admin</a>

    </div>
  </div>
  <!-- /.center -->



  <script>
    $(document).ready(function() {
      end_loader();
    })
  </script>
</body>

</html>