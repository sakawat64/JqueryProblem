<?php 
$all_area = GetAllbyCond($db_link,"area","where 1"); ?>
<div class="col-md-2">
    <div class="form-group">
        <label class="small mb-1" for="area">Area</label>
        <select class="form-control" id="area" name="area">
          <?PHP
				while ($data = mysqli_fetch_assoc($all_area)){ ?>
					<option value=" <?= $data['area_id'] ?>"> <?= $data['area_name'] ?></option>
            <?php
				}
			?>
        </select>
    </div>
</div>
<div class="col-md-2">
    <div class="form-group">
        <label class="small mb-1" for="ubranch_id">Branch</label>
        <select class="form-control" id="ubranch_id" name="ubranch_id">
        	
        </select>
    </div>
</div>

<script>
	$('select[name="area"]').on('change', function() {
        var area_id = $(this).val();
        if(area_id) {
            $.getJSON('Customer/get_ajax_file.php', {area_id: $(this).val()}, function(data){
                var options = '';
                for (var x = 0; x < data.length; x++) {
                    options += '<option value="' + data[x]['branch_id'] + '">' + data[x]['branch_name'] + '</option>';
                }
                $('#ubranch_id').html(options);
            });
        }else{
            $('select[name="ubranch_id"]').empty();
            console.log("fff");
        }
    });
</script>

//in ajax file get_ajax_file.php

<?php
require '../../functions/functions.php';
checkLogin();
$db_link = connect();
if(isset($_GET['area_id'])){
    $area_id = $_GET['area_id'];
    $area = DetailsByCond($db_link,"area","area_id = $area_id ");
    $branchs = explode(',',$area['branch']);
    foreach($branchs as $branch)
    {
        $branch_dt = DetailsByCond($db_link,"ubranch","branch_id = $branch ");
       // var_dump($branch_dt);
        $json_data []= array (
             "branch_id" => $branch_dt['branch_id'],
             "branch_name" => $branch_dt['branch_name'],
              );
              
    }
    echo json_encode($json_data );
    //var_dump($json_data);
}
?>
