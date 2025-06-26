<style>
	/* #qr_holder{
		height:calc(100%);
	} */
#qr-reader{
	width:calc(60%);
}
@media (max-width: 720px){
	#qr-reader{
	width:calc(100%);
	/* height:calc(85%); */
}
/* #qr-reader__scan_region{
	height:calc(70%) !important;

}*/
#qr-reader__scan_region video{
	/* height: 60% !important; */
    object-fit: cover !important; 
 }
}
</style>
<script src="<?php echo base_url ?>dist/js/html5-qrcode.js"></script>
<script src="<?php echo base_url ?>dist/js/html5-qrcode-scanner.js"></script>
<script src="<?php echo base_url ?>dist/js/sweetalert.min.js"></script>
<div class="col-md-12 pt-3"  id ="qr_holder" align="center">
<div class="w-100 d-flex justify-content-end"><a class="btn btn-primary btn-rounded" id="startLive" href="./?page=registration&e=<?php echo $_GET['e'] ?>">Back</a></div>
	<div id="qr-reader" style=""></div>
    <div id="qr-reader-results"></div>
</div>
<input type="hidden" id="audience_id" value="">
<script>
    $(document).ready(function(){
        console.log(audience_id);
    });
    
	function _qsave(){
	    
        start_loader()
		var audience_id = $('#audience_id').val();
		
		var a = {audience_id:audience_id,event_id:'<?php echo $_GET['e'] ?>'};
		console.log(a);
		$.ajax({
                	url:_base_url_+'classes/Master.php?f=register',
                	method:'POST',
                	data:{audience_id:audience_id,event_id:'<?php echo $_GET['e'] ?>'},
                	error:err=>{
                		console.log(err)
                		alert_toast('An Error Occured.');
                        end_loader()
                	},
                	success:function(resp){
						resp = JSON.parse(resp)
                		if(resp.status == 1){
                			 swal({
							    title: resp.name,
							    text: 'Successfully Registered',
							    icon: 'success',
							    timer: 2000,
							    buttons: false,
							})
							
                		}else if(resp.status ==2){
                			swal({
							    title: 'Code is not Valid',
							    text: 'Code is not enrolled in this event',
							    icon: 'error',
							    timer: 200,
							    buttons: false,
							})
                		}else if(resp.status ==3){
                			swal({
							    title: 'Already Recorded',
							    text: resp.name+' is already recorded',
							    icon: 'error',
							    timer: 200,
							    buttons: false,
							})
                		}else{
                		alert_toast('An Error Occured.');

                		}
                        setTimout(function(){
                            $('#audience_id').val('')
                            end_loader()
                        },2100)
                        

                	}
                })
	}
    function docReady(fn) {
        // see if DOM is already available
        if (document.readyState === "complete"
            || document.readyState === "interactive") {
            // call on next available tick
            setTimeout(fn, 1);
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    }

    docReady(function () {
        var resultContainer = document.getElementById('qr-reader-results');
        var lastResult, countResults = 0;
        function onScanSuccess(qrCodeMessage) {
            if (qrCodeMessage !== $('#audience_id').val()) {
                ++countResults;
                lastResult = qrCodeMessage;
				$('#audience_id').val(qrCodeMessage)
				// uni_modal("","<?php echo base_url ?>establishment/temperature.php","md-large")
                _qsave()
                
            }
        }

        // var html5QrcodeScanner = new Html5QrcodeScanner(
        //     "qr-reader", { fps: 5, qrbox: 200 });
		if($(window).height()  <= '720'){
			var html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", { fps: 5, qrbox: 150 });
		}else{
			var html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", { fps: 5, qrbox: 150 });
		}
		
        html5QrcodeScanner.render(onScanSuccess);
    });
   $(document).ready(function(){
	   console.log($(window).height() - $('.top-head').height())
	   $('.main-container').height($(window).height() - $('.top-head').height())
   })

</script>