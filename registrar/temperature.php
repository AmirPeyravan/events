<div class="container-fluid" align="center">
    <h3><b>Scan your Temperature</b></h3>
    <input class="form-control form-control-lg text-center" id="temp" type="number" onload="$(this).focus()" step="any"/>
    <br>
    <button class="btn btn-primary" type="button" id="get_scanned">Get Scanned</button>
    <button class="btn btn-primary" type="button" id="save">Save</button>
</div>

<style>
	#uni_modal .modal-footer{
		display: none;
	}
</style>
<script>
    function updateButt(){
        start_loader();
        fetch("https://api.thingspeak.com/channels/1320472/fields/1.json?api_key=EBZJI2XM9TQ3BNQV&results=1")
            .then (response => {
                return response.json();
            })
            .then (data => {
                console.log(data.feeds);
                const result = data.feeds.map(Temp =>{
                    return Temp.field1
                }).join("");
                document.getElementById("temp").innerHTML = result;
                console.log(result+" DONE");
                $('#temperature').val(result)
                $('#temp').val(result)
                end_loader()
            }).catch(error =>{
                console.log(error);
        })
    }

    $(document).ready(function(){
        $('#temp').keyup(function(){
            $('#temperature').val($(this).val())
        })
        setTimeout(() => {
         $('#temp').trigger('focus');
        }, 1000);
		if($('#uni_modal .modal-header button.close').length <= 0)
		$('#uni_modal .modal-header').append('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
        $('#uni_modal').on("click.dismiss.bs.modal",function(){
        })
        $('#get_scanned').click(function(){
            // _qsave()
            updateButt()
        })
        $('#save').click(function(){
            _qsave()
        })
    })

    
</script>
