<?php
// filename: myAjaxFile.php
// some PHP
    $advert = array(
        'ajax' => 'Hello world!',
        'advert' => $row['adverts'],
     );
    echo json_encode($advert);
?>
<script>
$.ajax({
        url : 'myAjaxFile.php',
        type : 'POST',
        data : data,
        dataType : 'json',
        success : function (result) {
           alert(result['ajax']); // "Hello world!" alerted
           console.log(result['advert']) // The value of your php $row['adverts'] will be displayed
        },
        error : function () {
           alert("error");
        }
    })
</script>