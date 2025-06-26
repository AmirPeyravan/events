<?php
$event_id = isset($_GET['eid'])? $_GET['eid'] : '';

?>
<style>
.alert{
	border: 1px solid #f9000059;
	background-color: #f9000059
}
</style>
<div class="col-md-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
			<form action="" id="filter-frm">
			<div class="col-md-12">
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group">
							<label for="event_id">Event</label>
							<select name="event_id" id="event_id" class="custom-select custom-select-sm select2">
								<?php
									$event= $conn->query("SELECT * FROM event_list order by title asc");
									while($row=$event->fetch_assoc()):
										if(empty($event_id))
										$event_id = $row['id'];
								?>
								<option value="<?php echo $row['id'] ?>" <?php echo $event_id == $row['id'] ? 'selected' : '' ?>><?php echo(ucwords($row['title'])) ?></option>
							<?php endwhile; ?>
							</select>
						</div>
					</div>
					<div class="col-sm-2">
						<button class="btn btn-sm btn-primary mt-4"><i class="fa fa-filter"></i> Filter</button>
						<button class="btn btn-sm btn-success mt-4" onclick="_Print()"><i class="fa fa-print"></i> Print</button>
					</div>
				</div>
			</div>
			</form>
			<hr class="border-primary">
			<div id="report-tbl-holder">
				<h4 class="text-center">Report</h4>
				<hr>
				<?php 
					$qry = $conn->query("SELECT * FROM event_list where id = '$event_id'");
					foreach($qry->fetch_array() as $k => $v){
						if(!is_numeric($k)){
							$$k = $v;
						}
					}
				?>
				<div class="callout">
					<div class="row">
						<div class="col-md-6">
							<dl>
								<dt>Event Title</dt>
								<dd><?php echo $title ?></dd>
							</dl>
							<dl>
								<dt>Event Venue</dt>
								<dd><?php echo $venue ?></dd>
							</dl>
							<dl>
								<dt>Event Description</dt>
								<dd><?php echo $description ?></dd>
							</dl>
						</div>
						<div class="col-md-6">
							<dl>
								<dt>Event Start</dt>
								<dd><?php echo date("M d, Y h:i A",strtotime($datetime_start)) ?></dd>
							</dl>
							<dl>
								<dt>Event End</dt>
								<dd><?php echo date("M d, Y h:i A",strtotime($datetime_end)) ?></dd>
							</dl>
							<?php 
							if($limit_registration == 1):
							?>
							<dl>
								<dt>Registration Cut-off Time</dt>
								<dd><?php echo date("M d, Y h:i A",strtotime($datetime_end.' + '.$limit_time.' minutes')) ?></dd>
							</dl>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<table id="report-tbl" class="table table-stripped table-bordered">
					<thead>
						<tr>
							<th>#</th>
							<th>Date/Time</th>
							<th>Name</th>
							<th>Contact</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
						echo $event_id;
						
						$qry = $conn->query("SELECT a.*,r.id as rid,r.date_created as rdate FROM registration_history r inner join users a on a.id =r.user_id where r.event_id = '{$event_id}' order by r.id asc ");
			
						while($row=$qry->fetch_assoc()):
						?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("M d, Y h:i A",strtotime($row['rdate'])) ?></td>
							<td><?php echo ucwords($row['firstname'] .' ' . $row['lastname'] ) ?></td>
							<td><?php echo ucwords($row['number']) ?></td>
						</tr>
						<?php endwhile; ?>
						<?php if($qry->num_rows <=0): ?>
							<tr>
								<th class="text-center" colspan="6">No Data.</th>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	

	<noscript>
		<style>
			table{
				border-collapse:collapse;
				width: 100%;
			}
			tr,td,th{
				border:1px solid black;
			}
			td,th{
				padding: 3px;
			}
			.text-center{
				text-align: center;
			}
			.text-right{
				text-align: right;
			}
			p{
				margin: unset;
			}
			.alert{
				border: 1px solid #f9000059;
				background-color: #f9000059
			}
		</style>
	</noscript>
	<script>
		function _Print(){
			start_loader();
			var ns = $('noscript').clone()
			var report = $('#report-tbl-holder').clone()
			var head = $('head').clone()

			var _html = report.prepend(ns.html())
				_html.prepend(head)
			var nw = window.open('','_blank',"height=900,width=1200");
			nw.document.write(_html.html())
			nw.document.close()
			nw.print()

			setTimeout(function(){
				nw.close()
				end_loader()
			})
		}
		$(document).ready(function(){
			$('.select2').select2();
			$('#filter-frm').submit(function(e){
				e.preventDefault()
				location.replace(_base_url_+'admin/?page=reports&eid='+$('#event_id').val())
			})
		})
	</script>
</div>