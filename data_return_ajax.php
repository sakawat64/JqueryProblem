        <small class="padding_3_10_px label btn-danger">
            Total Due Bill : <span class="badge due_bill_amount">0000 </span>&nbsp; taka
        </small>
<!-- Ajax calling ajax_data_return.php-->
<?php
 $total_bill_for_month = $obj->get_total_bill_amount();
    $totalCus = $obj->Total_Count('tbl_agent', 'ag_status=1');
    $totalPaid = $obj->Total_Count('tbl_agent', 'ag_status=1 and pay_status=0 and due_status=0');
    $totalNonPaid = $obj->Total_Count('tbl_agent', 'ag_status=1 and pay_status=1');

    echo json_encode(array("duePayment" => $total_due_amount, "totalBill" => $total_bill_for_month, "totalCus" => $totalCus, "totalPaid" => $totalPaid, "totalNonPaid" => $totalNonPaid));
?>

<script>
        $(document).ready(function () {
            $.post("view/ajax_action/ajax_data_return.php", function (data) {

                $('div#header_area span.due_bill_amount').html(data.duePayment.toLocaleString());

            }, "json");
        });
    </script>