<?php
date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$date = date('Y-m-d');
//$date        = date('Y-m-d');
$Month = date('m', strtotime($date));
$Year = date('Y', strtotime($date));
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$flag = isset($_GET['key']) ? "1" : "0";
$where = "";
$title = " " . date("M Y");

$where = "MONTH(entry_date)='$Month' and YEAR(entry_date)='$Year'";
$where_or = "MONTH(entry_date)='$Month' and YEAR(entry_date)='$Year' or MONTH(inactive_date)='$Month' and YEAR(inactive_date)='$Year'";
$all = $obj->Total_Count("vw_agent", $where_or);
$active = $obj->Total_Count("tbl_agent", $where . " AND ag_status='1'");
//$inactive = $obj->Total_Count("tbl_agent", $where . " AND ag_status='0'");
$inactive = $obj->Total_Count("vw_agent", "MONTH(inactive_date)='$Month' and YEAR(inactive_date)='$Year' AND ag_status='0'");


?>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
<style>
    .all-class {
        background: #23659f;
        border-color: #23578d;
        box-shadow: -1px 6px 4px #4e4e4e !important
    }

    .active-class {
        background-color: #308a30;
        border-color: #387c38;
        box-shadow: -1px 6px 4px #4e4e4e !important
    }

    .inactive-class {
        background-color: #bd2a26;
        border-color: #9c2824;
        box-shadow: -1px 6px 4px #4e4e4e !important
    }
</style>
<div class="row">
    <div class="col-md-12">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12 bg-grey-800">
        <h4 id="new_joining_title">New Joining Customer Information for <span id="show_date"><?php echo $title ?></span></h4>
    </div>
</div>

<div class="row" id="date_search_field">
    <div class="panel panel-success">
        <div class="panel-heading">
            <b>Please select Date to search Client</b>
        </div>
        <div class="panel-body">
            <form id="date_search" action="" method="POST">
                <div class="col-md-5">
                    <div class="form-group">
                        <input type="text" class="form-control datepicker" placeholder="Form Date" name="datefrom"
                               required>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <input type="text" class="form-control datepicker"
                               placeholder="To Date" name="dateto" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="search" class="btn btn-primary"> Search</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="row" id="">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div id="client_list" class="btn-group btn-group-justified">

                    <a id="all" class="btn btn-primary all-class">All Client - <span
                                class="badge all_client"><?php echo $all ?></span> </a>
                                <?php if($obj->hasPermission($ty, 'active_inactive')){ ?>
                    <a id="active" class="btn btn-success">Active Client - <span
                                class="badge all_active"><?php echo $active ?></span></a>
                    <a id="inactive" class="btn btn-danger">Inactive Client - <span
                                class="badge all_inactive"><?php echo $inactive ?></span></a>
                   <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="table-responsive" style="font-size:12px;">
            <?php
        if ($ty == 'SA' ||  $obj->hasPermission($ty, 'total_bill')) {
            ?>
            <h4 class="text-teal-800">Total Bill Amount: <span id="totalBillAmount"></span></h4>
            <?php
        }
        ?>
            <table class="table table-striped" id="view_new_customer_datatable">
                <thead>
                <tr class="bg-slate-800">
                    <th class="col-md-1">Action</th>
                    <th class="col-md-1">Customer ID</th>
                    <th class="col-md-1">Customer Name</th>
                    <th class="col-md-1">Address</th>
                    <th class="col-md-1">House No</th>
                    <th class="col-md-1">Mobile No</th>
                    <th class="col-md-1">Speed</th>
                    <th class="col-md-1">Bill</th>
                    <th class="col-md-1">Zone</th>
                    <th class="col-md-1">IP</th>
                    <th class="col-md-1">Connect</th>
                    <th class="col-md-1">Status</th>
                </tr>

                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {


        var today = new Date();
        var lastDay = new Date(today.getFullYear(), (today.getMonth() + 1), 0);

        // get first day of month and last day of month
        var dateFrom = today.getFullYear() + '-' + (today.getMonth() + 1) + '-1';
        var dateTo = lastDay.getFullYear() + '-' + (lastDay.getMonth() + 1) + '-' + lastDay.getDate();
        var clientStatus = 'All';
        var title = 'New Joining Customer Information for <?php echo $title ?>';
        
        var table = $('#view_new_customer_datatable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'print',
                    text: 'Print Client',
                    exportOptions: {
                        columns: [1,3,4,5,6,7,8]
                    },
                    title: function () {
                        return title
                    },
                    customize: function ( win ) {
                        $(win.document.body).css( 'font-size', '12px' ),
                        $(win.document.body).find( 'h1' ).addClass( 'text-center' ).css( 'font-size', '20px' );
                        $(win.document.body).find( 'table' ).addClass( 'container' ).css( 'font-size', 'inherit' );
                    }
                }
            ],
            "ajax": 'view/ajax_action/ajax_view_agent.php?datefrom=' + dateFrom + '&dateto=' + dateTo + '&stat=all',
            "deferRender": true,
            "order": [[0, 'asc']],
            "columns": [
                {
                    "data": 'ag_id',
                    "render": function (data, type, row, meta) {

                        return '<a href="?q=add_payment&token1=' + data + '" class="btn btn-sm btn-success pull-left"  >Ad.Pay</a>' +
                            '<?php echo ( $obj->userWorkPermission($acc,'edit')) ? '<a target="_blank" class="btn btn-sm btn-primary pull-left"  href="?q=edit_agent&token=\' + data + \'"> Edit &nbsp; </a>' : ''; ?> '
                    }
                },
                {
                    "data": 'cus_id',
                    render: function (data, type, row) {
                        return '<a href="?q=view_customer_payment_individual&token2= ' + row['ag_id'] + '"> ' + data + ' </a>'
                    }
                },
                {
                    "data": 'ag_name'
                },
                {
                    "data": 'ag_office_address',
                },
                {
                    "data": 'house_no',
                },
                {
                    "data": 'ag_mobile_no'
                },
                {
                    "data": 'mb'
                },
                {
                    "data": 'taka',
                    "searchable": false,
                },
                {
                    "data": 'zone_name',
                },
                {
                    "data": 'ip',
                },
                {
                    "data": 'connection_date',
                },
                {
                    "data": 'ag_status',
                    "render": function (data, type, row, meta) {

                        if (data == 1) {
                            return '<buttton class="padding_5_px btn-success btn-xs">Active</span></button>';

                        } else {
                            return '<buttton class="padding_5_px btn-danger btn-xs">Inactive</span></button>';

                        }
                    }
                },
            ],
            "initComplete": function (settings, json) {
                var stat="all";
                showtotalbillAmount(status);
            },

        });
        function showtotalbillAmount(status){
            $.get('view/ajax_action/ajax_view_agent.php?datefrom=' + dateFrom + '&dateto=' + dateTo + '&stat='+status+'', function(data, status){
                $('#totalBillAmount').html(data.total_bill + ' Taka');

            }, 'json');


        }

        $('div#date_search_field').on('submit', 'form#date_search', function (event) {

            event.preventDefault();
            var fromDateInput = $(this).find('input[name="datefrom"]').val().split('-');
            var toDateInput = $(this).find('input[name="dateto"]').val().split('-');

            dateFrom = inputDateFormt(fromDateInput);
            dateTo = inputDateFormt(toDateInput);

            table.ajax.url('view/ajax_action/ajax_view_agent.php?datefrom=' + dateFrom + '&dateto=' + dateTo + '&stat=all').load();
            showDate(dateFrom, dateTo); // to show date in title
            clientCountByDate(dateFrom, dateTo); // to show the number of client in such date.
            changeTitle(dateFrom, dateTo, 'All') // to change the title of print page.
            clientStatus = 'All'; // default client status is all cause showing all client
            showtotalbillAmount(status);

        });

        function clientCountByDate(dateFrm, dateT) {

            // go to several page to get client number and place this data to html place.
            var url = 'view/ajax_action/ajax_count_active_inactive_client.php';
            $.get(url, {datefrom: dateFrom, dateto: dateTo}, function (countData) {

                $('#client_list span.all_client').html(countData.all_client);
                $('#client_list span.all_active').html(countData.active_client);
                $('#client_list span.all_inactive').html(countData.inactive_client);

            }, "json");

        }

        $('#all').click(function () {

            table.ajax.url('view/ajax_action/ajax_view_agent.php?datefrom=' + dateFrom + '&dateto=' + dateTo + '&stat=all').load();
            clientStatus = 'All';
            addRemoveClass($(this), 'all-class'); // to selected the button called all
            changeTitle(dateFrom, dateTo, 'All'); // to change the title of the print page
            showtotalbillAmount(status);
        });
        $('#active').click(function () {

            table.ajax.url('view/ajax_action/ajax_view_agent.php?datefrom=' + dateFrom + '&dateto=' + dateTo + '&stat=1').load();
            clientStatus = 'Active';
            addRemoveClass($(this), 'active-class'); // to selected the button called active
            changeTitle(dateFrom, dateTo, 'Active'); // to change the title of the print page
            showtotalbillAmount(1);
        });

        $('#inactive').click(function () {

            table.ajax.url('view/ajax_action/ajax_view_agent.php?datefrom=' + dateFrom + '&dateto=' + dateTo + '&stat=0').load();
            clientStatus = 'Inactive';
            addRemoveClass($(this), 'inactive-class'); // to selected the button called inactive
            changeTitle(dateFrom, dateTo, 'Inactive'); // to change the title of the print page
            showtotalbillAmount(0);
        });

        function addRemoveClass(thisdata, className) {
            // to show selected the button

            $('#all').removeClass('all-class');
            $('#active').removeClass('active-class');
            $('#inactive').removeClass('inactive-class');
            thisdata.addClass(className);
        }


        function showDate(dateStartInput, dateEndInput) {
            // to convert the date formate and show those in title

            var dateOne = new Date(dateStartInput);
            var dateOneFormated = dateOne.getDate() + "/ " + (dateOne.getMonth() + 1) + "/ " + dateOne.getFullYear();
            var dateTwo = new Date(dateEndInput);
            var dateTwoFormated = dateTwo.getDate() + "/ " + (dateTwo.getMonth() + 1) + "/ " + dateTwo.getFullYear();

            $('span#show_date').html(dateOneFormated + " to " + dateTwoFormated);
        }
        
        function changeTitle(dateFromTitle, dateToTitle, statusTitle){
            // to change the title for print page

            title = 'New ' + statusTitle + ' Customer Information of '+ dateFromTitle +' to ' + dateToTitle ;

        }

        $('.datepicker').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'dd-mm-yyyy',
        });
    });
</script>
<script type="application/javascript"
        src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="application/javascript"
        src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js "></script>



//file for ajax_view_agent.php




<?php
session_start();

if (!empty($_SESSION['UserId'])) {
//========================================
include '../../model/oop.php';
include '../../model/Bill.php';
//include '../../model/SSP.php';

$obj = new Controller();
$bill = new Bill();
//========================================

$user_id = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;
$FullName = isset($_SESSION['FullName']) ? $_SESSION['FullName'] : NULL;
$UserName = isset($_SESSION['UserName']) ? $_SESSION['UserName'] : NULL;
$PhotoPath = isset($_SESSION['PhotoPath']) ? $_SESSION['PhotoPath'] : NULL;
$ty = isset($_SESSION['UserType']) ? $_SESSION['UserType'] : NULL;


/* =============================================
 * =============================================
 *  Here we use raw query only enhance performance
 *  When the agent number is more than 1500+
 *  It take significant time to show them.*
 * =============================================
 * =============================================*/

    date_default_timezone_set('Asia/Dhaka');
    $date_time = date('Y-m-d g:i:sA');
    $ip_add = $_SERVER['REMOTE_ADDR'];
    $userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

    $allAgentData = array();
    $total_bill = 0;

    if (isset($_GET['zone'])) {

        $zone = $_GET['zone'];
        $sql = "SELECT * FROM vw_agent WHERE zone='$zone' ORDER BY `vw_agent`.`ag_id` DESC";

    }
    elseif (isset($_GET['bid'])) {

        $bid = $_GET['bid'];
        $sql = "SELECT * FROM vw_agent WHERE billing_person_id='$bid' ORDER BY `vw_agent`.`ag_id` DESC";

    }



    elseif (isset($_GET['status'])) {

        $status = $_GET['status'];
        $sql = "SELECT * FROM vw_agent WHERE ag_status='$status' ORDER BY `vw_agent`.`ag_id` DESC";

    }


    elseif (  isset($_GET['datefrom']) && isset($_GET['dateto'])  && isset($_GET['stat']) ) {

        $dateFrom = $_GET['datefrom'];
        $dateto = $_GET['dateto'];
        $stat = $_GET['stat'];

        if($stat === '1' ) {
            $sql = "SELECT * FROM vw_agent WHERE entry_date BETWEEN '" . $dateFrom . "' and '" . $dateto . "' AND  ag_status='".$stat."'  ORDER BY `vw_agent`.`ag_id` DESC";
        }elseif ($stat === '0'){
            $sql = "SELECT * FROM vw_agent WHERE inactive_date BETWEEN '" . $dateFrom . "' and '" . $dateto . "' AND  ag_status='".$stat."'  ORDER BY `vw_agent`.`ag_id` DESC";
        } else{
            $sql = "SELECT * FROM vw_agent WHERE entry_date BETWEEN '" . $dateFrom . "' and '" . $dateto . "' and ag_status=1 or inactive_date BETWEEN '" . $dateFrom . "' and '" . $dateto . "' and ag_status=0  ORDER BY `vw_agent`.`ag_id` DESC";
        }

    }



    else{
        $sql = "SELECT * FROM vw_agent ORDER BY `vw_agent`.`ag_id` DESC";
    }


    $q = $obj->con->prepare($sql);
    $q->execute();
    $i = 1;
    while ($row = $q->fetch(PDO::FETCH_ASSOC)) {

        $row['sl'] = $i++;
        $allAgentData[] = $row;

        if($row['ag_status'] == 1){ // for calculate active client total bill amount
            $total_bill += $row['taka'];
        }
        if($row['ag_status'] == 0){ // for calculate active client total bill amount
            $total_bill += $row['taka'];
        }
    }

    /* =================================================
     *  Below code is written for show total bill
     * above the table as calculated with this.
     * Datatable not support the additional data without table
     ===================================================*/
    if(isset($_GET['view_agent_total_bill']) && !empty($_GET['view_agent_total_bill'])){

        echo json_encode((array('total_bill' => $total_bill)));
    }else{

        echo json_encode(array('data' => $allAgentData,'total_bill' => $total_bill));
    }


} else {
    header("location: include/login.php");
}
?>