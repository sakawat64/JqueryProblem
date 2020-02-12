<div class="col-md-1 col-md-offset-11">
    <button type="submit" class="btn btn-primary pull-right" onclick="printDiv('month_print')">Print Statement</button>
</div>
<div class="row" id="month_print">
	<!-- printed element -->
</div>
<script type="text/javascript">
	function printDiv(divName) {
        $(".print").css("margin-left", "0px");
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
