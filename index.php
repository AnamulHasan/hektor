<?php
define("SERVER","localhost");
define("USER","root");
define("PSWD","");
define("DB","skill_test");
$db=new MySQLi(SERVER,USER,PSWD,DB);

$table_data = $db->query("SELECT `year`, `month`, `day`, `hours`, `minutes`, `seconds`, `fontSize`, `bgColor`, `bgImage`, `counterColor`, `labelColor` FROM `countdown` ORDER BY id DESC LIMIT 1");
list($endYear,$endMonth,$endDay,$endHours,$endMinutes,$endSeconds,$fontSize,$bgColor,$bgImage,$counterColor,$labelColor) = $table_data->fetch_row();

$endTime = strtotime("$endYear-$endMonth-$endDay $endHours:$endMinutes:$endSeconds");
$stored = $endYear."-".$endMonth."-".$endDay." ".$endHours.":".$endMinutes.":".$endSeconds;


$error = array();
if(isset($_POST['submit'])){
    $file_path = '';
    $endYear = htmlspecialchars(trim($_POST['year']));
    $endMonth = htmlspecialchars(trim($_POST['month']));
    $endDay = htmlspecialchars(trim($_POST['day']));
    $endHours = htmlspecialchars(trim($_POST['hours']));
    $endMinutes = htmlspecialchars(trim($_POST['minutes']));
    $fontSize = htmlspecialchars(trim($_POST['fontSize']));
    if(empty($fontSize)){
        array_push($error, "<span class='text-danger'>Please enter font size.</span>");
    } else if($fontSize<15){
        array_push($error, "<span class='text-danger'>Please enter font size more than 14px.</span>");
    }
    $bgColor = htmlspecialchars(trim($_POST['bgColor']));
    if(isset($_FILES["bgImage"])==true){
        if(empty($_FILES["bgImage"]["name"])==true){
            array_push($error, "<span class='text-danger'>Please choose an image</span>");
        }else{
            $allowed=array('jpg', 'jpeg', 'gif', 'png');

            $file_name=$_FILES['bgImage']['name'];
            $arr=explode('.', $file_name);
            $file_extn=strtolower(end($arr));
            $file_temp=$_FILES['bgImage']['tmp_name'];
            $file_size=$_FILES['bgImage']['size'];

            if($file_size<20000){
                if(in_array($file_extn,$allowed)==true){
                    $file_path="resource/img/".substr(md5(time()),1,10).".".$file_extn;
                    move_uploaded_file($file_temp,$file_path);
                }else{
                    array_push($error, "<span class='text-danger'>Incorrect file type! Allowed only: ".implode(', ',$allowed)."</span>");
                }
            }else{
                array_push($error, "<span class='text-danger'>Maximum file size allowed 8kb</span>");
            }
        }
    }
    $counterColor = htmlspecialchars(trim($_POST['counterColor']));
    $labelColor = htmlspecialchars(trim($_POST['labelColor']));

    $endTime = strtotime("$endYear-$endMonth-$endDay $endHours:$endMinutes:00");
    if($endTime<time()){
        array_push($error, "<span class='text-danger'>Please enter future date / time.</span>");
    }


    if(!empty($endYear) && !empty($endMonth) && !empty($endDay) && !empty($endHours) && !empty($endMinutes) && empty($error)){
        $db->query("INSERT INTO `skill_test`.`countdown` (`year`, `month`, `day`, `hours`, `minutes`, `seconds`, `fontSize`, `bgColor`, `bgImage`, `counterColor`, `labelColor`) VALUES ('$endYear', '$endMonth', '$endDay', '$endHours', '$endMinutes', '00', '$fontSize', '$bgColor', '$file_path', '$counterColor', '$labelColor');");
        header("location:/hektor/");
    };

}//end of isset

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hektor Challenge</title>
    <link href="resource/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .col-lg-4, .col-md-4, .col-sm-4, .col-xs-4 {
            padding-left: 0;
        }
        h2 {
            text-align: center;
        }
        hr {
            border-top: 2px dotted #eee;
        }
        #count-down {
            text-align: center;
            background-color: <?php echo isset($bgColor)?$bgColor.";":"#000;";?>;
            background: url(<?php echo $bgImage;?>) no-repeat center center fixed;
            background-size: cover;
        }
        #count-down small {
            color: <?php echo isset($labelColor)?$labelColor.";":"#000;";?>;
        }
        #date_div {
            font-size: <?php echo isset($fontSize)?$fontSize."px;":"25px;";?>;
            color: <?php echo isset($counterColor)?$counterColor.";":"#000;";?>;
            background-color: rgba(209, 224, 214, 0.50);

        }

    </style>
    <script>
        var endTime = <?php echo $endTime; ?>*1000;

        function countdown () {
            var t  = new Date();
            var currentTime = t.getTime();
            var diff = endTime - currentTime;
            if(endTime>currentTime){
            var seconds = parseInt((diff/1000)%60);
            var minutes = parseInt((diff/(1000*60))%60);
            var hours = parseInt((diff/(1000*60*60))%24);
            var days = parseInt(((diff)/(60*60*24)) / 1000);

            days = (days < 10) ? "0" + days : days;
            hours = (hours < 10) ? "0" + hours : hours;
            minutes = (minutes < 10) ? "0" + minutes : minutes;
            seconds = (seconds < 10) ? "0" + seconds : seconds;

            var cc = days + 'D: ' + hours + 'H: ' + minutes + 'M: ' + seconds + 'S';
            }else{
                cc = 'x';
            }
            document.getElementById("date_div").innerHTML = cc;
        }
    </script>
</head>
<body>
<div class="container">
    <h2><a href="/hektor/">Hektor technology</a></h2>
    <hr>
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
        <form action="#" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="endDate">Select CountDown's Occasion Date / Time:</label><br>
                <select name="year" class="input-sm" id="endDate">
                    <option value="" selected="selected">&nbsp; Year </option>
                    <?php
                    $var_y='';
                    $year_start = date("Y");
                    $year_end = $year_start+1;
                    for($var_y=$year_start; $var_y<=$year_end; $var_y++){
                        echo "<option value='$var_y'>&nbsp; $var_y </option>";
                    }

                    ?>
                </select>
                <select name="month" class="input-sm" id="endDate">
                    <option value="" selected="selected"> Month </option>
                    <?php
                    $months = array(1=>'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');

                    foreach($months as $key => $value){
                        echo "<option value='$key'>&nbsp; $value </option>";
                    }
                    ?>
                </select>
                <select name="day" class="input-sm" id="endDate">
                    <option value="" selected="selected">&nbsp; Day </option>
                    <?php
                    for($day=1; $day<=31; $day++){
                        $day=($day<10)?"0".$day:$day;
                        echo "<option value='$day'>&nbsp; $day </option>";
                    }
                    ?>
                </select><br><br>
                <select name="hours" class="input-sm" id="endDate">
                    <option value="" selected="selected">&nbsp; Hours </option>
                    <?php
                    for($hours=1; $hours<=24; $hours++){
                        $hours = ($hours<10)?"0".$hours:$hours;
                        echo "<option value='$hours'>&nbsp; $hours </option>";
                    }
                    ?>
                </select>
                <select name="minutes" class="input-sm" id="endDate">
                    <option value="" selected="selected">&nbsp; Minutes </option>
                    <?php
                    for($minutes=1; $minutes<=60; $minutes++){
                        $minutes = ($minutes<10) ? "0".$minutes : $minutes;
                        echo "<option value='$minutes'>&nbsp; $minutes </option>";
                    }
                    ?>
                </select>
            </div>
            <hr/>
            <div class="form-group">
                <label for="font-size">Font Size:</label><br>
                <small>Please enter font size more than 14 px unit.</small><br>
                <div class="col-lg-4 col-md-4 col-sm-8 col-xs-8">
                    <input type="text" name="fontSize" class="form-control input-sm" id="font-size">
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label for="bg-color">Counter Background Color:</label><br>
                <small>The counter background color.</small><br>
                <div class="col-lg-4 col-md-4 col-sm-8 col-xs-8">
                    <input type="color" name="bgColor" class="form-control input-sm" id="bg-color">
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label for="files">Counter Background Image:</label><br>
                <small>The counter background image.</small><br>
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                    <input type="file" name="bgImage"  class="form-control input-sm" id="files">
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label for="counter-color">Counter Color:</label><br>
                <small>The counter color.</small><br>
                <div class="col-lg-4 col-md-4 col-sm-8 col-xs-8">
                    <input type="color" name="counterColor" class="form-control input-sm" id="counter-color">
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label for="label-color">Label Color:</label><br>
                <small>The label color.</small><br>
                <div class="col-lg-4 col-md-4 col-sm-8 col-xs-8">
                    <input type="color" name="labelColor" class="form-control input-sm" id="label-color">
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                </div>
            </div>
        </form>
    </div><!--end of left div-->
    <div id="count-down" class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
        <h1>Count Down Timer Goes Here!</h1>
        <small>Special moments :  <?php echo $stored; ?></small>
        <br><br>
        <div id="date_div">
            <script> countdown(); setInterval('countdown()',1000); </script>
        </div>
        <br><br>
        <div>
            <?php
                if(!empty($error)){
                  foreach ( $error as $err){
                    echo $err;
                      echo "<br>";
                  };
                };
            ?>
        </div>
    </div><!--end of right div-->
</div>
<br><br>
</body>
</html>