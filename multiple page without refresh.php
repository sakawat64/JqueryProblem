<?php

date_default_timezone_set('Asia/Dhaka');
$date_time = date('Y-m-d g:i:sA');
//$date        = date('Y-m-d');
$ip_add = $_SERVER['REMOTE_ADDR'];
$userid = isset($_SESSION['UserId']) ? $_SESSION['UserId'] : NULL;

if (isset($_POST['logo_image_upload'])) {

    $file_temp = $_FILES['logo_image']['tmp_name'];
    $file_name = $_FILES['logo_image']['name'];
    $file_size = $_FILES['logo_image']['size'];

    $permited = array('jpg', 'jpeg', 'png', 'gif');
    $div = explode('.', $file_name);
    $file_ext = strtolower(end($div));

    if ($file_size > 1048567) {

        $obj->notificationStore('Image Size should be less then 1MB!');
    } elseif (in_array($file_ext, $permited) === false) {

        $obj->notificationStore("You can upload only:-" . implode(', ', $permited) . "");
    } else {
        include_once('model/smart_resize_image.function.php');
        $file = $file_temp;
        $resizedFile = 'logo.png';
        if (smart_resize_image($file, null, 225, 105, true, $resizedFile, false, false, 100)) {
            echo '<script> window.location = "?q=software_setting"; </script>';
        } else {
            $obj->notificationStore('Sorry Update Failed');
        }
    }
}



if (isset($_POST['invoice_style_submit'])) {

    if ( $obj->Update_data('tbl_setting', [ 'value' => $_POST['invoice_style'] ], 'field="invoice"') ) {
        $obj->notificationStore('Invoice Style Updated Successfully', 'success');
    } else {
        $obj->notificationStore('Invoice Style Updated Failed');
    }
}

if (isset($_POST['sms_info_submit'])) {
    $smsUserUpdate = $obj->Update_data('tbl_setting', ['value' => str_replace("'", '', $_POST['sms_cname'])], 'field="sms" AND other_parameter="company_name"');
    $smsUserUpdate = $obj->Update_data('tbl_setting', ['value' => str_replace("'", '', $_POST['sms_user'])], 'field="sms" AND other_parameter="user"');
    $smsPassUpdate = $obj->Update_data('tbl_setting', ['value' => str_replace("'", '', $_POST['sms_password'])], 'field="sms" AND other_parameter="pass"');
    $smsSenderUpdate = $obj->Update_data('tbl_setting', ['value' => str_replace("'", '', $_POST['sms_sender'])], 'field="sms" AND other_parameter="sender"');
    $supportNumUpdate = $obj->Update_data('tbl_setting', ['value' => str_replace("'", '', $_POST['support_num'])], 'field="sms" AND other_parameter="support_num"');

    if ($smsPassUpdate && $smsUserUpdate && $smsSenderUpdate && $supportNumUpdate) {
        $obj->notificationStore('SMS Info Updated Successfully', 'success');
    } else {
        $obj->notificationStore('SMS Info Updated Failed');
    }
}
//
if (isset($_POST['excel_info_submit'])) {
    $excelNameUpdate = $obj->Update_data('tbl_setting', ['value' => str_replace("'", '', $_POST['excel_company_name'])], 'field="excel" AND other_parameter="name"');
    $excelTitleUpdate = $obj->Update_data('tbl_setting', ['value' => str_replace("'", '', $_POST['excel_company_title'])], 'field="excel" AND other_parameter="title"');
    $excelKeywordUpdate = $obj->Update_data('tbl_setting', ['value' => str_replace("'", '', $_POST['excel_company_keyword'])], 'field="excel" AND other_parameter="keyword"');

    if ($excelNameUpdate && $excelTitleUpdate && $excelKeywordUpdate) {
        $obj->notificationStore('Excel File Info Updated Successfully', 'success');
    } else {
        $obj->notificationStore('Excel File Info Updated Failed');
    }
}

?>

<style>
    .setting-list li.list-group-item {
        padding: 3px !important;
    }

    ul.setting-list li.active a {
        background: #0a564c !important;
    }

</style>

<div class="row bg-teal-800">
    <div class="col-md-12">
        <h4 id="preview_date">Change Setting Of This Software</h4>
    </div>
</div>
<hr>

<div class="row">
    <div class="col-md-12">
        <?php $obj->notificationShow(); ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-3">
        <ul id="nav-tabs-wrapper" class="setting-list list-group nav nav-pills nav-stacked">
            <li class="list-group-item active">
                <a href="#logo_change" data-toggle="tab"><i class="glyphicon glyphicon-picture"></i> Logo </a>
            </li>
            <li class="list-group-item">
                <a href="#sms_panel" data-toggle="tab"> <i class="glyphicon glyphicon-envelope"></i> SMS Setting</a>
            </li>
            <li class="list-group-item">
                <a href="#print_invoice" data-toggle="tab"> <i class="glyphicon glyphicon-print"></i> Print Invoice</a>
            </li>
            <li class="list-group-item">
                <a href="#excell_info" data-toggle="tab"> <i class="glyphicon glyphicon-file"></i> Excell Sheet</a>
            </li>
        </ul>
    </div>

    <div class="col-sm-9">

        <div class="tab-content">

            <div role="tabpanel" class="tab-pane fade in active" id="logo_change">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Change Logo</h3>
                    </div>
                    <div class="panel-body">
                        <form method="post" enctype="multipart/form-data" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="file" name="logo_image">
                                        <p class="help-block">Make Sure Logo Image Size 750 X 350</p>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="form-group text-center">
                                <div class="col-sm-12">
                                    <input type="submit" name="logo_image_upload" value="Update Logo Image"
                                           class="btn btn-primary"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane fade" id="sms_panel">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">SMS Setting</h3>
                    </div>
                    <div class="panel-body">
                        <form method="post" class="form-horizontal" enctype="multipart/form-data" action="">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">SMS Panel Company Name</label>
                                <div class="col-sm-8">
                                    <input type="text" required name="sms_cname"
                                           value="<?php echo $obj->getSettingValue('sms', 'company_name') ?>"
                                           class="form-control" placeholder="Company Name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">SMS Panel User Name</label>
                                <div class="col-sm-8">
                                    <input type="text" required name="sms_user"
                                           value="<?php echo $obj->getSettingValue('sms', 'user') ?>"
                                           class="form-control" placeholder="User Name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">SMS Panel Password</label>
                                <div class="col-sm-8">
                                    <input type="text" required name="sms_password"
                                           value="<?php echo $obj->getSettingValue('sms', 'pass') ?>"
                                           class="form-control" placeholder="Password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">SMS Panel Sender</label>
                                <div class="col-sm-8">
                                    <input type="text" required name="sms_sender"
                                           value="<?php echo $obj->getSettingValue('sms', 'sender') ?>"
                                           class="form-control" placeholder="SMS Sender">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">SMS Support Number</label>
                                <div class="col-sm-8">
                                    <input type="text" required name="support_num"
                                           value="<?php echo $obj->getSettingValue('sms', 'support_num') ?>"
                                           class="form-control" placeholder="Support Number">
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <div class="col-sm-12">
                                    <input type="submit" name="sms_info_submit" value="Update SMS Info"
                                           class="btn btn-primary"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane fade in" id="print_invoice">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Print Invoice Setting</h3>
                    </div>
                    <div class="panel-body">
                        <form method="post" class="form-horizontal" action="">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Select Invoice Style</label>
                                <div class="col-sm-8">

                                    <select name="invoice_style" class="form-control">
                                        <option <?php echo ($obj->getSettingValue('invoice') == 3) ? 'selected' : ''; ?> value="3">Three Invoice per Page</option>
                                        <option <?php echo ($obj->getSettingValue('invoice') == 1) ? 'selected' : ''; ?> value="1">One Invoice per Page</option>
                                    </select>
                                    <p class="help-block">Invoice print in Bill Collection Page</p>
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <div class="col-sm-12">
                                    <input type="submit" name="invoice_style_submit" value="Update Invoice PDF Style"
                                           class="btn btn-primary"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane fade in" id="excell_info">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Excel Sheet Info Setting</h3>
                    </div>
                    <div class="panel-body">
                        <form method="post" class="form-horizontal" action="">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Set Company Name</label>
                                <div class="col-sm-8">
                                    <input type="text" required name="excel_company_name"
                                           value="<?php echo $obj->getSettingValue('excel', 'name') ?>"
                                           class="form-control" placeholder="Company Name">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Set Company Title</label>
                                <div class="col-sm-8">
                                    <input type="text" required name="excel_company_title"
                                           value="<?php echo $obj->getSettingValue('excel', 'title') ?>"
                                           class="form-control" placeholder="Company Title">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Set Company Keyword</label>
                                <div class="col-sm-8">
                                    <input type="text" required name="excel_company_keyword"
                                           value="<?php echo $obj->getSettingValue('excel', 'keyword') ?>"
                                           class="form-control" placeholder="Company Keyword">
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <div class="col-sm-12">
                                    <input type="submit" name="excel_info_submit" value="Update Excel File Info"
                                           class="btn btn-primary"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<hr>
