<style>
    .datatable tbody td:nth-child(0) {
        text-align:center;
    }
</style>
<br>
<div class="card card-outline card-primary">
    <div class="col-md-12 p-2">
        <table class="table table-striped table-bordered" id="track-tbl">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Temperature</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#track-tbl').DataTable({
            "processing": true,
            "serverSide": true,
            "order":false,
            "ajax": {
                "url": _base_url_+"classes/Establishment.php?f=tracks",
                "type": "POST"
            },
            "columns": [
                { "data": "time", "orderable":false },
                { "data": "date", "orderable":false },
                { "data": "name", "orderable":false },
                { "data": "temperature", "orderable":false },
            ]
        })
    })
</script>