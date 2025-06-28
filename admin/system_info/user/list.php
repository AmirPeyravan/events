<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
		    <?php 
		        if($_settings->userdata('login_type') == 1){
		            $sql = "SELECT *,concat(firstname,' ',lastname) as name FROM `users` where id != '{$_settings->userdata('id')}' order by concat(firstname,' ',lastname) asc ";
		        }else if($_settings->userdata('login_type') == 'event_manager'){
		            $owner = $_SESSION['userdata']['id'];
		            $sql = "SELECT *,concat(firstname,' ',lastname) as name FROM `users` where id != '{$_settings->userdata('id')}' AND owner ='{$owner}'  order by concat(firstname,' ',lastname) asc ";
		        }
		        $qry = $conn->query($sql);
		       ?>
		       
			<div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary new_user" href="javascript:void(0)"><i class="fa fa-plus"></i> Add New</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<colgroup>
					<col width="10%">
					<col width="15%">
					<col width="25%">
					<col width="25%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Image</th>
						<th>Name</th>
						<th>Username</th>
						<th>Type</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					while($row= $qry->fetch_assoc()):
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td class="text-center"><img class="direct-chat-img border" src="<?php echo validate_image($row['avatar']) ?>" alt="Image" style="float:unset;"></td>
						<td><b><?php echo ucwords($row['name']) ?></b></td>
						<td><b><?php echo $row['username'] ?></b></td>
						<td><b><?php echo ($row['type'] == 1) ? "Admin" : "Registrar" ?></b></td>
						<td class="text-center">
		                    <div class="btn-group">
		                        <a href="javascript:void(0)" data-id='<?php echo $row['id'] ?>' class="btn btn-primary btn-flat manage_user">
		                          <i class="fas fa-edit"></i>
		                        </a>
		                        <button type="button" class="btn btn-danger btn-flat delete_user" data-id="<?php echo $row['id'] ?>">
		                          <i class="fas fa-trash"></i>
		                        </button>
	                      </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>

	$(document).ready(function(){
		$('.new_user').click(function(){
			uni_modal("New User","./user/manage.php",'mid-large')
		})
		$('.manage_user').click(function(){
			uni_modal("Manage User","./user/manage.php?id="+$(this).attr('data-id'),'mid-large')
		})
		$('.delete_user').click(function(){
		_conf("Are you sure to delete this User?","delete_user",[$(this).attr('data-id')])
		})
		$('#list').dataTable()
	})
	function delete_user($id){
	   // var formData = new FormData();
		start_loader()
		$.ajax({
			url:_base_url_+'classes/Users.php?f=delete',
			method:'POST',
			data:{id:$id},
			error:err=>{
					console.log(err)
					alert_toast("an error occured","error")
					end_loader()
			},
			success:function(resp){
			    end_loader()
				if(resp==1){
					location.reload()
				}
			}
		})
	}
</script>