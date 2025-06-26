<?php
require_once('../../config.php');

if (isset($_GET['id']) && !empty($_GET['id'])) {
	$qry = $conn->query("SELECT * FROM event_audience where id = {$_GET['id']}");
	foreach ($qry->fetch_array() as $k => $v) {
		if (!is_numeric($k)) {
			$$k = $v;
		}
	}
}
?>

<form action="" id="audience-frm">
	<div id="msg" class="form-group"></div>
	<input type="hidden" name='id' value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
    <?php if (isset($_GET['id'])): ?>
    	<!-- Edit Mode -->
    	<div class="form-row">
    		<div class="form-group col-md-6">
    			<label for="name" class="control-label">Fullname</label>
    			<input type="text" class="form-control form-control-sm" name="name" id="name" required value="<?= isset($name) ? $name : '' ?>">
    		</div>
    
    		<div class="form-group col-md-6">
    			<label for="contact" class="control-label">Contact</label>
    			<input type="text" class="form-control form-control-sm" name="contact" id="contact" required value="<?= isset($contact) ? $contact : '' ?>">
    		</div>
    	</div>
	<?php else: ?>
	<!-- Add Mode -->
	<div class="form-row">
		<div class="form-group col-md-6">
			<label for="name" class="control-label">Fullname</label>
			<input type="text" class="form-control form-control-sm" name="name[]" id="name" required>
		</div>

		<div class="form-group col-md-6">
			<label for="contact" class="control-label">Contact</label>
			<input type="text" class="form-control form-control-sm" name="contact[]" id="contact" required>
		</div>
	</div>

	<div id="additional-users"></div>
    <input type="hidden" name="owner" value="<?php echo $_settings->userdata('id') ?>" id="owner" />
    
	<button type="button" id="add-user" class="btn btn-secondary">Add More Users</button>
	<?php endif; ?>
</form>

<script>
	$(document).ready(function() {
		$('.select2').select2();

		$('#add-user').click(function() {
			$('#additional-users').append(`
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="name" class="control-label">Fullname</label>
                        <input type="text" class="form-control form-control-sm" name="name[]" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="contact" class="control-label">Contact</label>
                        <input type="text" class="form-control form-control-sm" name="contact[]" required>
                    </div>
                </div>
            `);
		});

		$('#audience-frm').submit(function(e) {
			e.preventDefault();
			start_loader();
			if ($('.err_msg').length > 0)
				$('.err_msg').remove();
			$.ajax({
				url: _base_url_ + 'classes/Master.php?f=save_audience',
				data: new FormData($(this)[0]),
				cache: false,
				contentType: false,
				processData: false,
				method: 'POST',
				type: 'POST',
				dataType: 'json',
				error: err => {
					console.log(err);
					alert_toast("an error occurred", "error");
					end_loader();
				},
				success: function(resp) {
					if (resp.status == 'success') {
						location.reload();
					}else if(resp.status == 'skipped') {
					    location.reload();
						alert_toast("some audience exist", 'error');
					}else{
					    alert_toast("An error occurred.", 'error');
					}
					end_loader();
				}
			});
		});
	});
</script>
