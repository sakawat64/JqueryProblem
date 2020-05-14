
<div class="form-group">
  <label for="doctor" class="control-label mb-10">Doctor </label>
  <select class="form-control" name= "doctor" id = "doctor" required autofocus>

  <option>Select Doctor</option>

  </select>
</div>
<div class="form-group">
  <label for="schedule" class="control-label mb-10">Schedule Date  *</label>
      <input type="text" name="schedule" class="form-control" id="schedule" placeholder="Schedule" autocomplete="off" required autofocus>
</div>

<script type="text/javascript">
  $('#doctor').on('change', function (e) {
        
        
        
//        weekday['Mon']="Monday";        //1
//        weekday['Tue']="Tuesday";       //2
//        weekday['Wed']="Wednesday";     //3
//        weekday['Thu']="Thursday";      //4
//        weekday['Fri']="Friday";        //5
//        weekday['Sat']="Saturday";      //6
//        weekday['Sun']="Sunday";        //0
          //  $("#appointmentform").empty();
           // $("#timelist").empty();
           // $("#timeset").show();

        dayNameId = [];

           // hospital_id = $(this).find(':selected').data('id1');
           var doctor = $(this).val();

            $.ajax({
            method: "post",
            url: 'calender.php',
            data: {
           // hospital_id: hospital_id,
            'doctor': doctor
            },

            success: function (data) {
//                console.log(data)
                array_val=JSON.parse(data);
                calc(array_val)
            }

            });
        });
  function calc(array_val) 
  {
    var dayNameId=[];
    for (var i = 0; i < array_val.length; i++) {
        dayNameId[i] = array_val[i]; 
    }
    
    $("#schedule").datepicker({
        beforeShowDay: function (dt) {
            dmy = dt.getDay();
            
//                        console.log(dayNameId);
            if ($.inArray(dmy, dayNameId) == -1) {
                //firstDay: 4;
                return [false, "Unavailable"];
            } else {
                return [true, "Available"];
            }
        },
        minDate: 0,
        maxDate: "+60d",
        showAnim: "clip",
        dateFormat: "yy-mm-dd",
//                        onSelect: function (inst) {
//                            var seldate = $(this).datepicker("getDate");
//                            seldate = seldate.toDateString();
//                            seldate = seldate.split(" ");
//                            dayOfWeek = weekday[seldate[0]];
            /*$("#disabledtime").remove();
            $("#timeset").hide();
            $("#timelist").show();
            $("#appointmentform").empty();
            $("#Monday").hide();
            $("#Wednesday").hide();
            $("#Sunday").hide();
            $("#Saturday").hide();
            $("#Tuesday").hide();
            $("#Thursday").hide();
            $("#Friday").hide();*/
//                            $("#" + dayOfWeek).show();
//                        }
    });
}
$('#doctor').on('change',function(){

       var doctor = $(this).val();
       $( "#schedule" ).datepicker("refresh");
       $("#schedule").datepicker("destroy");
       

        if(doctor)
        {
          $.ajax({

            type: "post",
            url: 'view/ajax-request-files/department.php',
            data: 
            {
              'doctor' : doctor
            },
            success: function(res)
            {
              $('#sname').html(res);
            }

          });
        }
        else
        {
          $('#sname').html('<option value="">Select Schedule</option>');
        }

       });
</script>

//Calender.php
<?php
<?php
include_once '../../vendor/autoload.php';
\App\Session::init();

 use App\DBConnection;

 $doctor = new \App\customer\Customer();
$schedule=new \App\schedule\Schedule();
 if(isset($_POST['doctor']))
 {
  $schedule->set($_POST);
  $results2 = $schedule->getDays();
//    $weekendArr = array();
    $weekendArr = explode(",",$results2['Weekly_Days']);
     
    $link=[];
    foreach ($weekendArr as $key) {
        if($key==1)
            {
            //return  "Friday";
            $link[]=5;
            }
            if($key==2)
            {
             $link[]=6;
            }
            if($key==3)
            {
             $link[]=0;//"Sunday";
            }
            if($key==4)
              {
              $link[]=1;//"Monday";
              }
              if($key==5)
             {
               $link[]=2;//"Tuesday";
             }
              if($key==6)
            {
             $link[]=3;//"Wednesday";
            }
             if($key==7)
             {
                $link[]=4;//"Thursday";
             } 
        }
     echo json_encode($link);
    ?>
    <?php
 }
?>
?>