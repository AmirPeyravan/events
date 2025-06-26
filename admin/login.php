<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
 <?php require_once('inc/header.php') ?>
<body class="hold-transition login-page">
  <script>
    start_loader()
  </script>
<div class="login-box">
  
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="./" class="h1"><b>Login</b></a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form id="login-frm" action="" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="username" placeholder="Username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
            <div class="col-12">
                <input type="checkbox" id="manager" name="manager" value="1"><label>Event Manager</label>
            </div>
        </div>
        <div class="row">
          <div class="col-8">
            <a href="<?php echo base_url ?>">Go to Portal</a>
          </div>
        
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          
        </div>
      </form>
     
      
    </div>
    
  </div>
  
</div>



<!--<script src="plugins/jquery/jquery.min.js"></script>-->

<!--<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>-->

<!--<script src="dist/js/adminlte.min.js"></script>-->

<script>
  $(document).ready(function(){
    end_loader();
  })
</script>
</body>
</html>