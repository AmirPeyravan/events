<?php 
require_once('../../config.php');
if(isset($_GET['id']) && !empty($_GET['id'])){
	$qry = $conn->query("SELECT * FROM users where id = {$_GET['id']}");
	foreach($qry->fetch_array() as $k => $v){
		if(!is_numeric($k)){
			$$k = $v;
		}
	}
}
?>
<form action="" id="people-frm">
	<div class="row">
		<div class="col-md-6">
			<div id="msg" class="form-group"></div>
			<input type="hidden" name='id' value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
			<div class="form-group">
				<label for="firstname" class="control-label">First Name</label>
				<input type="text" class="form-control form-control-sm" name="firstname" id="firstname" value="<?php echo isset($firstname) ? $firstname : '' ?>" required>
			</div>
			<div class="form-group">
				<label for="lastname" class="control-label">Last Name</label>
				<input type="text" class="form-control form-control-sm" name="lastname" id="lastname" value="<?php echo isset($lastname) ? $lastname : '' ?>" required>
			</div>
			<div class="form-group">
				<label for="username" class="control-label">Username</label>
				<input type="text" class="form-control form-control-sm" name="username" id="username" value="<?php echo isset($username) ? $username : '' ?>" required>
			</div>
			<div class="form-group">
					<label for="password">Password</label>
					<input type="password" name="password" id="password" class="form-control" value="" autocomplete="off" required>
			</div>
			
			<div class="form-group">
					<label for="number">whatsapp Number</label>
					<input type="text" name="number" id="number" class="form-control" value="<?php echo isset($number) ? $number : '' ?>" required>
			</div>
		</div>
		
		<div class="col-md-6">
			<?php if($_settings->userdata('login_type') == 1) :?>
			<input type="hidden" name="owner" value="<?php echo $_settings->userdata('id') ?>" id="owner" />
            <div class="form-group">
                <label for="type" class="control-label">User Type</label>
                <select name="type" id="type" class="custom-select custom-select-sm">
                    <option value="2" <?php echo (isset($type) && $type == 1) ? "selected" : '' ?>>Registrar</option>
                    <option value="1" <?php echo (isset($type) && $type == 1) ? "selected" : '' ?>>Administrator</option>
                    <option value="0" <?php echo (isset($type) && $type == 0) ? "selected" : '' ?>>1 Event Manager</option>
                </select>
            </div>
            
            <?php elseif($_settings->userdata('login_type') == 'event_manager'): ?>
                <input type="hidden" name="type" value="2" id="type" />
                <input type="hidden" name="owner" value="<?php echo $_settings->userdata('id') ?>" id="owner" />
            <?php endif ?>
            
            <?php if($_settings->userdata('login_type') == 1) :?>
			<div class="form-group">
				<label for="" class="control-label">Image</label>
				<div class="custom-file">
		          <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
		          <label class="custom-file-label" for="customFile">Choose file</label>
		        </div>
			</div>
			<div class="form-group d-flex justify-content-center">
				<img src="<?php echo validate_image(isset($avatar) ? $avatar : '') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
			</div>
			<?php endif ?>
				
			<?php if(isset($_GET['id']) && ($_GET['id'] > 0)): ?>
            <input type="hidden"  name="avatar" value="<?php echo $avatar ?>">
			<div class="form-group">
				<div class="icheck-primary">
					<input type="checkbox" id="resetP" name="preset">
					<label for="resetP">
						Check to reset password
					</label>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
</form>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$(document).ready(function(){
		$('.select2').select2();
		$('#city_id').change(function(){
			var id = $(this).val();
			console.log($('#zone_id').find("[data-city='"+id+"']").length)
			$('#zone_id').find("[data-city='"+id+"']").show()
		$('#zone_id').select2();
		})
		$('#people-frm').submit(function(e){
			e.preventDefault()
			start_loader()
			if($('.err_msg').length > 0)
				$('.err_msg').remove()
			$.ajax({
				url:_base_url_+'classes/Users.php?f=save',
				data: new FormData($(this)[0]),
			    cache: false,
			    contentType: false,
			    processData: false,
			    method: 'POST',
			    type: 'POST',
				error:err=>{
					console.log(err)

				},
				success:function(resp){
				if(resp == 1){
					location.reload();
				}else if(resp == 3){
					var _frm = $('#people-frm #msg')
					var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> Person already exists.</div>"
					_frm.prepend(_msg)
					_frm.find('input#username').addClass('is-invalid')
					$('[name="code"]').focus()
				}else{
					alert_toast("An error occured.",'error');
				}
					end_loader()
				}
			})
		})
	})
</script>