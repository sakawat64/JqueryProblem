<?php
$mikrotikConnect = false;
require('model/Mikrotik.php');
if ($obj->tableExists('mikrotik_user')) {

    $mikrotikLoginData = $obj->details_by_cond('mikrotik_user', 'id = 1');
    $mikrotik = new Mikrotik($mikrotikLoginData['mik_ip'], $mikrotikLoginData['mik_username'], $mikrotikLoginData['mik_password']);

    if ($mikrotik->checkConnection()) {

        $mikrotikConnect = true;

    }
}

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

$data = $obj->details_by_cond("tbl_agent", "ag_status='1' ORDER BY ag_id DESC");

if (($data['ag_id'] + 1) < 10) {
    $STD = "CUS000";
} else if (($data['ag_id'] + 1) < 100) {
    $STD = "CUS000";
} else if (($data['ag_id'] + 1) < 1000) {
    $STD = "CUS00";
} else if (($data['ag_id'] + 1) < 10000) {
    $STD = "CUS0";
} else {
    $STD = "CUS";
}
$STD .= $data['ag_id'] + 1;


if (isset($_POST['zoneAddSubmit'])) {

    $zone_form_data = array(
        'zone_name' => $_POST['zoneName'],
        'created_by' => $userid
    );
    $addZone = $obj->Reg_user_cond("tbl_zone", $zone_form_data, " ");
    if ($addZone) {

        $obj->notificationStore('Zone Insert Successfully', 'success');
        echo '<script>window.location = "?q=add_agent";</script>';
    } else {
        $obj->notificationStore('Zone Insert Failed');
    }
}
if (isset($_POST['packageAddSubmit'])) {

    $package_form_data = array(
        'package_name' => $_POST['packageName'],
        'net_speed' => $_POST['packageNetSpeed'],
        'bill_amount' => $_POST['packageBillAmount'],
        'created_by' => $userid
    );
    $addPack = $obj->Reg_user_cond("tbl_package", $package_form_data, " ");
    if ($addPack) {

        $obj->notificationStore('Package Insert Successfully', 'success');
        echo '<script>window.location = "?q=add_agent";</script>';
    } else {
        $obj->notificationStore('Package Insert Failed');
    }

}

?>

<div class="row">
    <div class="col-md-12">
        <?php $obj->notificationShowRedirect(); ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12 bg-slate-800" style="margin-top:20px; margin-bottom: 15px;">
        <h4>
            <strong>Create New Customer as <?php echo $STD;
                echo (isset($mikrotik)) ? '/ Secret <small>(in Mikrotik)</small>' : '' ?> </strong>
        </h4>
    </div>
</div>

<div class="row" style="padding:10px; font-size: 12px;">
    <form role="form" action="add/add_agent_action.php" method="post" style="padding:10px; font-size: 12px;">
        <div class="row">
            <?php
            if (isset($mikrotik)) {

                if ($mikrotikConnect) { ?>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Client IP / Secret</label>
                            <input type="text" name="ip" value="" class="form-control"
                                   id="clientIp"
                                   placeholder="Provide Unique Ip For Mikrotik" required="required"
                                   onkeypress="return noSpace(event)">
                            <div id="checkavailablityclientIp" class="text-center">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="Provide Password">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">Select Profile</label>
                            <select class="form-control" required name="package" id="packageList">
                                <option></option>
                                <?php foreach ($mikrotik->profileStatus() as $singlePackage) { ?>
                                    <option value="<?php echo $singlePackage['name'] ?>"><?php echo $singlePackage['name'] ?></option>
                                <?php }// foreach  ?>
                            </select>
                        </div>
                        <div class="form-group" style="padding-top:5px">
                            <label>Service</label>
                            <input type="text" class="form-control" disabled value="PPoE">
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="col-md-12">
                        <div class="alert alert-danger text-center">
                            <strong>Sorry!</strong> Could not connected to Mikrotik. Please <a class="alert-link"
                                                                                               href="index.php?q=mikrotik_configure">configure</a>
                            and then try again.
                        </div>
                    </div>
                <?php }
            } ?>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Customer Name/Company Name</label>
                    <input type="text" name="name" class="form-control" required="required">
                </div>
                <div class="form-group">
                    <label>SMS Mobile No</label>
                    <input onkeypress="return numbersOnly(event)" type="text" name="mobile" class="form-control">
                </div>
                <div class="form-group">
                    <label>Others Mobile Number</label>
                    <input type="text" name="regularmobile" class="form-control">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label>Mac Address</label>
                    <input type="text" name="macaddress" class="form-control">
                </div>
                <div class="form-group">
                    <label>National Id/Passport Number</label>
                    <input type="text" name="national_id" class="form-control"
                    >
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea class="form-control" name="details" id="ResponsiveDetelis" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label class="pull-left">Select Billing Person</label>
                    <select class="form-control" name="billingperson" required="required">
                        <option>Select</option>
                        <?php foreach ($obj->view_all('tbl_employee') as $singleUser) { ?>
                            <option
                                    value="<?php echo $singleUser['id'] ?>"><?php echo $singleUser['employee_name'] ?></option>
                        <?php }// foreach ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>IP address</label>
                    <input type="text" name="ip_address" value="" class="form-control" placeholder="Ip Address"
                               required="required">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="text" name="passwordd" value="" class="form-control" placeholder="Password"
                               required="required">
                </div>

            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="pull-left">Select Zone</label>
                    <button type="button" style="padding-top: 0px;padding-bottom : 0px;"
                            class="btn btn-default border-white btn-sm pull-right" data-toggle="modal"
                            data-target="#addZoneModal">
                        <span class="glyphicon glyphicon-plus"></span> Add Zone
                    </button>
                    <select class="form-control" name="zone" required="required">
                        <option></option>
                        <?php foreach ($obj->view_all('tbl_zone') as $singleZone) { ?>
                            <option
                                    value="<?php echo $singleZone['zone_id'] ?>"><?php echo $singleZone['zone_name'] ?></option>
                        <?php }// foreach ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Running Month Amount</label>
                            <input onkeypress="return numbersOnly(event)" type="text" name="opening_amount"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-md-6" id="running_month_amount">
                        <label>Having Due in Running Month Amount</label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="checkbox" style="margin-top:5px">
                                    <label>
                                        <input type="checkbox" name="running_month_due_have" value="1"><b>Due</b>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <input onkeypress="return numbersOnly(event)" type="text" placeholder="Due amount"
                                       disabled name="running_month_due_amount"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Connection Charge</label>
                            <input onkeypress="return numbersOnly(event)" type="text" name="connect_charge"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-md-6" id="connection_charge_amount">
                        <label>Having Due in Connection Charge</label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="checkbox" style="margin-top:5px">
                                    <label>
                                        <input type="checkbox" name="connection_charge_due_have" value="1"><b>Due</b>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <input onkeypress="return numbersOnly(event)" type="text" placeholder="Due amount"
                                       disabled name="connection_charge_due_amount"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Connection Date</label>
                    <input id="datepicker" type="text" name="con_date" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label class="pull-left">Select Package</label>
                    <button type="button" style = "padding-top: 0px;padding-bottom : 0px;" class="btn btn-default btn-sm pull-right" data-toggle="modal" data-target="#addPackageModal">
                        <span class="glyphicon glyphicon-plus"></span> Add package
                    </button>
                    <select class="form-control" name="package" id="packageList">
                        <option></option>
                        <?php foreach ($obj->view_all('tbl_package') as $singlePackage) { ?>
                            <option data-speed = "<?php echo $singlePackage['net_speed'] ?>" data-bill = "<?php echo $singlePackage['bill_amount'] ?>" value="<?php echo $singlePackage['package_name'] ?>"><?php echo str_replace('_', '-', $singlePackage['package_name']) ?></option>
                        <?php }// foreach  ?>
                    </select>
                </div>
                <div class="form-group" id ="billAmount">
                    <label>Bill Amount</label>
                    <input onkeypress="return numbersOnly(event)" type="text" name="taka" class="form-control"
                           placeholder="Provide Bill Amount">
                </div>
                <div class="form-group" id = "netSpeed">
                    <label>Speed</label>
                    <input type="text" name="mb" class="form-control"
                           placeholder="Provide Net Speed">
                </div>
                <div class="form-group" id>
                    <label>Bill Date</label>
                    <input onkeypress="return numbersOnly(event)" type="text" name="bill_date" class="form-control"
                           placeholder="Provide Bill Date">
                </div>

                <?php if (isset($mikrotik)) { ?>
                    <div class="form-group">
                        <label>Mikrotik Disconnect Day &nbsp;
                            <small><span class="alert-warning"><span class="text-danger">*</span> Unpaid User Discounted on This Date of Month</span>
                            </small>
                        </label></label>
                        <input type="number" onkeypress="return numbersOnly(event)" name="mikrtk_disconnect_date"
                               class="form-control" value="10">
                    </div>
                <?php } else { ?>
                    <div class="form-group">
                        <label>User Name</label>
                        <input type="text" name="ip" value="" class="form-control" placeholder="Provide Unique Ip"
                               required="required"
                               onkeypress="return noSpace(event)">
                    </div>
                <?php } ?>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" required="required" name="status" id="status">
                                <option></option>
                                <option selected value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-top: 18px;">

                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="sms_check" checked value="1"><b>SMS Notification</b>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>

              <div class="form-group">
                    <label>Remarks</label>
                    <textarea class="form-control" name="remarks" id="remarks" rows="2"></textarea>
                </div>


            </div>
        </div>
        <!--            row-->
        <div class="row text-center">
            <button type="submit" class="btn btn-success" name="submit">Save New Customer</button>
        </div>
    </form>
</div>
<!-- Modal Start-->
<div class="modal fade" id="addPackageModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">Insert New package</h4>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="row" style="padding:0 10px;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Package Name</label>
                                <input type="text" name="packageName" class="form-control"  placeholder="Provide Package Name" required = "required" >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Net Speed</label>
                                <input type="text" name="packageNetSpeed" class="form-control"
                                       required = "required" placeholder="Provide Net Speed">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bill Amount</label>
                                <input type="text" onkeypress="return numbersOnly(event)" name="packageBillAmount" class="form-control" placeholder="Provide Bill Amount" required = "required" >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <button type="submit" class="btn btn-success pull-left" name="packageAddSubmit"> Save Package
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>

    </div>
</div>

<!-- Modal Start-->
<div class="modal fade" id="addZoneModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center">Insert New Zone</h4>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="row" style="padding:0 10px;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="row">
                                    <label>Zone Name</label>
                                    <input type="text" name="zoneName" class="form-control"
                                           placeholder="Provide Zone Name">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-default" name="zoneAddSubmit"> Insert Zone
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function numbersOnly(e) // Numeric Validation
    {
        var unicode = e.charCode ? e.charCode : e.keyCode
        if (unicode != 8) {
            if ((unicode < 2534 || unicode > 2543) && (unicode < 48 || unicode > 57)) {
                return false;
            }
        }
    }


    function noSpace(e) {
        if (e.which == 32) {
            return false;
        }
    }

    $(document).ready(function () {
        // look client Id is unique or not --->
        $('#clientIp').focusout(function () {
            var clientIpData = $(this).val();
            if (clientIpData != '') {
                var url = 'view/ajax_action/add_ajax_data.php';
                var postData = {check: clientIpData}
                $.post(url, postData, function (data) {
                    $('#checkavailablityclientIp').html(data);
                });
            } else {
                $('#checkavailablityclientIp').html('');
            }
        });

        // while package change autometic bill and speed will be updated
        $('select#packageList').on('change', function () {
            var package = $('select#packageList option:selected').val();
            var speed = $('select#packageList option:selected').data('speed');
            var bill = $('select#packageList option:selected').data('bill');

            $("div#billAmount input[name='taka']").val(bill);
            $("div#netSpeed input[name='mb']").val(speed);

        }); // on change


        $('#running_month_amount').on('change', 'input[name="running_month_due_have"]', function () {

            var dueAmountText = $(this).closest('.row').find('input[name="running_month_due_amount"]');
            dueAmountText.val("");
            if (dueAmountText.is(':disabled')) {
                dueAmountText.prop('disabled', false);
            } else {
                dueAmountText.prop('disabled', true);
            }
        });

        $('#connection_charge_amount').on('change', 'input[name="connection_charge_due_have"]', function () {

            var dueAmountText = $(this).closest('.row').find('input[name="connection_charge_due_amount"]');
            dueAmountText.val("");
            if (dueAmountText.is(':disabled')) {
                dueAmountText.prop('disabled', false);
            } else {
                dueAmountText.prop('disabled', true);
            }
        });


        $('select[name="package"]').select2({
            placeholder: "Select a Profile",
            allowClear: true,
        });


        $('#datepicker').datepicker({
            autoclose: true,
            toggleActive: true,
            format: 'dd-mm-yyyy'
        });


    }); // document ready function
</script>


// in ajax file for check availability
<?php if (isset($_POST['check'])) {

        $checkIp = $_POST['check'];
        $findData = $obj->Total_Count('tbl_agent', "`ip` = '$checkIp'");
        if ($findData == 0) {
            echo '<p class="bg-success"><span  class="glyphicon glyphicon-remove text-success"></span> OK, You can proceed. </p>';
        } else {
            echo '<p class="bg-danger"><span  class="glyphicon glyphicon-remove text-danger"></span> Sorry this Client Id is already exists. </p>';
        }
    } ?>
