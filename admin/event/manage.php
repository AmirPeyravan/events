<?php
require_once('../../config.php');
if (isset($_GET['id']) && !empty($_GET['id'])) {
	$qry = $conn->query("SELECT * FROM event_list where id = {$_GET['id']}");
	foreach ($qry->fetch_array() as $k => $v) {
		if (!is_numeric($k)) {
			$$k = $v;
		}
	}
}
?>
<form action="" id="event-frm">
	<div id="msg" class="form-group"></div>
	<input type="hidden" name='id' value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
	<div class="form-group">
		<label for="title" class="control-label">Title</label>
		<input type="text" class="form-control form-control-sm" name="title" id="title" value="<?php echo isset($title) ? $title : '' ?>" required>
	</div>
	<div class="form-group">
		<label for="venue" class="control-label">Location(concat address with +)</label>
		<input type="text" placeholder="Hyatt+Regency+Kuwait" class="form-control form-control-sm" name="venue" id="venue" value="<?php echo isset($venue) ? $venue : '' ?>" required>
	</div>
	<div class="form-group">
		<label for="description" class="control-label">Description</label>
		<textarea type="text" class="form-control form-control-sm" name="description" id="description" required><?php echo isset($description) ? $description : '' ?></textarea>
	</div>
	<div class="form-group">
		<label for="datetime_start" class="control-label">DateTime Start</label>
		<input type="datetime-local" class="form-control form-control-sm" name="datetime_start" id="datetime_start" value="<?php echo isset($datetime_start) ? date("Y-m-d\\TH:i", strtotime($datetime_start)) : '' ?>" required>
	</div>
	<div class="form-group">
		<label for="datetime_end" class="control-label">DateTime End</label>
		<input type="datetime-local" class="form-control form-control-sm" name="datetime_end" id="datetime_end" value="<?php echo isset($datetime_end) ? date("Y-m-d\\TH:i", strtotime($datetime_end)) : '' ?>" required>
	</div>
	<div class="form-group">
		<label for="" class="control-label">Image</label>
		<div class="custom-file">
			<input type="file" class="custom-file-input rounded-circle" id="customFile" name="image" onchange="displayImg(this,$(this))">
			<label class="custom-file-label" for="customFile">Choose file</label>
		</div>
	</div>
	<div class="form-group d-flex justify-content-center">
		<img src="<?php echo validate_image(isset($image) ? $image : '') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
	</div>

	<input type="hidden" name="owner" value="<?php echo $_settings->userdata('id') ?>" id="owner" />

	<?php if (!$user_id) : ?>
		<div class="form-group">
			<label for="user_id" class="control-label">Assign To</label>
			<select name="customer[user_id][]" id="customer_user_id_" class="custom-select select2" required multiple>
				<option></option>

				<?php
				if ($_settings->userdata('login_type') == 'event_manager') {
					$owner = $_settings->userdata('id');
					$sql = "SELECT id,concat(name,' ',contact) as name FROM event_audience where owner = $owner order by concat(name,' ',contact) asc ";
				} else if ($_settings->userdata('login_type') == 1) {
					$sql = "SELECT id,concat(name,' ',contact) as name FROM event_audience  order by concat(name,' ',contact) asc ";
				}

				$qry = $conn->query($sql);
				while ($row = $qry->fetch_assoc()):
				?>
					<option value="<?php echo $row['id'] ?>" <?php echo isset($user_id) && $user_id == $row['id'] ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
				<?php endwhile; ?>

			</select>
		</div>
	<?php endif ?>

	<div class="form-group">
		<div class="icheck-primary">
			<input type="checkbox" id="limit_registration" name="limit_registration" value="1">
			<label for="limit_registration">
				Limited Time Of Registration Only
			</label>
		</div>
	</div>
	<div class="form-group" style="display:none">
		<label for="limit_time" class="control-label">Limit Registration Time (In Minutes)</label>
		<input type="number" min="0" class="form-control form-control-sm" name="limit_time" id="limit_time" value="<?php echo isset($limit_time) ? $limit_time : '' ?>">
	</div>
</form>
<script>
	function displayImg(input, _this) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#cimg').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	$(document).ready(function() {
		var ids;
		$('.select2').select2();
		$('#limit_registration').on('change input', function() {
			if ($(this).is(":checked") == true) {
				$('#limit_time').parent().show('slow')
				$('#limit_time').attr("required", true);
			} else {
				$('#limit_time').parent().hide('slow')
				$('#limit_time').attr("required", false);
			}
		})

		// Get Clients List 
		$("#customer_user_id_").change(function() {
			ids = $(this).val();
			console.log(ids);
		});




		$('#event-frm').submit(function(e) {

			var formData = new FormData();
			// title , venue, description , datetime_start , datetime_end , customer[user_id][], limit_time, owner,image
			formData.append("title", $("#title").val());
			formData.append("venue", $("#venue").val());
			formData.append("description", $("#description").val());

			formData.append("datetime_start", $("#datetime_start").val());
			formData.append("datetime_end", $("#datetime_end").val());
			formData.append("limit_time", $("#limit_time").val());
			formData.append("user_id", ids);
			formData.append("owner", $("#owner").val());
			// Get the image file from the input
			var imageFile = $('#customFile')[0].files[0];
			if (imageFile) {
				formData.append("image", imageFile);
			}

			e.preventDefault()
			start_loader()
			if ($('.err_msg').length > 0)
				$('.err_msg').remove()
			$.ajax({
				url: _base_url_ + 'classes/Master.php?f=save_event',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				method: 'POST',
				type: 'POST',
				dataType: 'json',
				error: err => {
					console.log(err)
					alert_toast("an error occured", "error")
					end_loader()
				},
				success: function(resp) {
					if (resp.status == 'success') {
						location.reload();
					} else if (esp.status == 'duplicate') {
						var _frm = $('#event-frm #msg')
						var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> Title already exists.</div>"
						_frm.prepend(_msg)
						_frm.find('input#title').addClass('is-invalid')
						$('[name="title"]').focus()
					} else {
						alert_toast("An error occured.", 'error');
					}
					end_loader()
				}
			})
		})
	})
</script>