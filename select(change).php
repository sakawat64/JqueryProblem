<div class="col-md-2 text-center pull-right" style="margin-top: -39px;margin-right: -12px;">
    <select class="form-control" name="category" required>
                    <option>Choose Category</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="ALL">All</option>
            </select>
    </div>

 <script>
$(document).ready(function(){
    $('select[name="category"]').on('change', function () {

            var catId = $(this).val();
                $.ajax({
                url:"ajax_view_client.php",
                method:"POST",
                data:{client_data:catId},
                dataType:"text",
                success:function(data){
                    $('#client-list').html(data);
                }
            });
        });
});
</script>