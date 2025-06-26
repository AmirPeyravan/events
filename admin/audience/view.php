<?php
require_once('../../config.php');
if(isset($_GET['id']) && !empty($_GET['id'])){
	$qry = $conn->query("SELECT a.*,e.title FROM event_audience a inner join event_list e on e.id = a.event_id where a.id = '{$_GET['id']}'");
	foreach($qry->fetch_array() as $k => $v){
		if(!is_numeric($k)){
			$$k = $v;
		}
	}
}
?>
<div class="row">
	<div class="col-md-12 mb-2 justifu-content-end">
		<button class="btn btn-sm btn-success float-right" type="button" id="print-card"><i class="fa fa-print"></i> Print</button>
	</div>
</div>
<div class="col-md-12" id="event_qr">
	<div class="form-group">
		<div class="form-group d-flex justify-content-center">
			<img src="<?php echo validate_image('temp/'.md5($id).'.png') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
		</div>
	</div>
	<hr class="border-primary">
	<dl>
		<dt>Event</dt>
		<dd><?php echo ucwords($title) ?></dd>
	</dl>
	<dl>
		<dt>Name</dt>
		<dd><?php echo ucwords($name) ?></dd>
	</dl>
</div>
<script>
	$('#print-card').click(function(){
		var ccts = $('#event_qr').clone()

		var nw = window.open('','_blank','height=600,width800');
		nw.document.write(ccts.html())
		nw.document.close()
		nw.print()
		setTimeout(function(){
			window.close()
		},750)
	})
	$(document).ready(function(){
		if($('#uni_modal .modal-header button.close').length <= 0)
		$('#uni_modal .modal-header').append('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
	})
</script>
<style>
	#uni_modal .modal-footer{
		display: none;
	}
	img#cimg{
		height: 150px;
		width: 150px;
		object-fit: contain;
	}
</style>