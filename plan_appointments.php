<?php 
session_start();
//ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php'); 
//$_SESSION['current_assigned_plan_code']="";
//$_SESSION['current_created_plancode'] = "RJKR10011003";
//$_SESSION['current_created_plancode'] = "ASPL10011001";
//$_SESSION['current_created_planname'] = "Diabetic Diet Plan";
//$_SESSION['current_created_planname'] = "Test Plan";
$plan_to_customize="";
$current_created_plancode = $_SESSION['current_created_plancode'];
$current_created_planname = $_SESSION['current_created_planname'];
//echo $plan_to_customize;exit;
if((isset($_REQUEST['hidden_value']))&&(!empty($_REQUEST['hidden_value']))){
	//echo "<pre>";print_r($_REQUEST);exit;
	$appointmentcount    		= mysql_real_escape_string(trim($_REQUEST['appointmentcount'])); //Total number of appointments present on screen
	$usedappointmentcount 		= mysql_real_escape_string(trim($_REQUEST['usedappointmentcount'])); //row ids of appointments present
	$appointmentids 			= explode(",", $usedappointmentcount);
	$datearray = array();
	$get_previous_dates = mysql_query("select AppointmentDate from APPOINTMENT_HEADER where PlanCode = '$current_created_plancode'");
	$date_count 				= mysql_num_rows($get_previous_dates);
	if($date_count > 0){
		while($datesentered 		= mysql_fetch_array($get_previous_dates)){
			$datepresent 			= $datesentered['AppointmentDate'];
			 array_push($datearray, $datepresent);
		} 		
	}
	//echo "<pre>";print_r($datearray);exit;
	foreach ($appointmentids as $ids) {
		if($ids != ""){
			$appointment_name =  mysql_real_escape_string(trim($_REQUEST["appointmentName$ids"]));
			if($appointment_name != ""){
			$doctor_name 				= mysql_real_escape_string(trim($_REQUEST["doctorName$ids"]));
			$appointment_date =  mysql_real_escape_string(trim($_REQUEST["appointment_date$ids"]));
			$appointment_date =  date('Y-m-d',strtotime($appointment_date));
			if (!in_array($appointment_date, $datearray)){
				  array_push($datearray, $appointment_date);
				  }
			$appointment_time =  mysql_real_escape_string(trim($_REQUEST["appointment_time$ids"]));
			$appointment_time =  date('H:i:s',strtotime($appointment_time));
			$appointment_reqs =  mysql_real_escape_string(trim($_REQUEST["requirements$ids"]));
			$insert_appointment_details = "insert into APPOINTMENT_DETAILS (`PlanCode`, `AppointmentDate`, `AppointmentTime`, `AppointmentShortName`, `DoctorsName`, `AppointmentRequirements`, `CreatedDate`, `CreatedBy`) values ('$current_created_plancode', '$appointment_date', '$appointment_time', '$appointment_name','$doctor_name','$appointment_reqs', now(), '$logged_userid');";
			//echo $insert_appointment_details;exit;
			$insert_appointment_run  	= mysql_query($insert_appointment_details);
		}
	}
	}
	//echo "<pre>";print_r($datearray);exit;
	foreach ($datearray as $date) {
		$insert_appointment_header = "insert into APPOINTMENT_HEADER (`PlanCode`, `AppointmentDate`, `CreatedDate`, `CreatedBy`) values ('$current_created_plancode', '$date', now(), '$logged_userid')";
			//echo $insert_appointment_header;exit;
			$insert_header_run  	= mysql_query($insert_appointment_header);
	}
	//$update_plan_header = mysql_query("update PLAN_HEADER set PlanStatus = 'A' where Plancode='$current_created_plancode'");
		header("Location:plan_appointments.php");
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper | Appointments</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/ndatepicker.css">
		<link href="css/bootstrap-timepicker.min.css" type="text/css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" href="css/planpiper.css">
		<link rel="stylesheet" type="text/css" href="fonts/font.css">
		<link rel="shortcut icon" href="images/planpipe_logo.png"/>       
        <script type="text/javascript">

        </script>
    </head>
    <body id="wrapper">
        <div class="col-sm-2 paddingrl0"  style="display:none;" id="sidebargrid">
		  	<?php include("sidebar.php");?>
		 </div>
		<div id="planpiper_wrapper" class="fullheight" class="col-sm-10 paddingrl0">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
			<?php include_once('top_header.php');?>
			</div>

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
			<div id="plantitle"><?php echo $current_created_planname;?>
			<button type="button" class="btns" align="right" id="finished_adding"><img src="images/finishAdd.png" style="height:20px;width:auto;margin-bottom:3px;">&nbsp;FINISH ADDING</button>
			</div>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<ul class="nav nav-pills nav-justified">
					<?php
					echo $modules;
					?>
					<!--<li role="presentation" class="navbartoptabs"><a href="plan_medication.php">MEDICATION</a></li>-->
					<!--<li role="presentation" class="active navbartoptabs" id="appointment"><a href="plan_appointments.php">APPOINTMENT</a></li>-->
					<!--<li role="presentation" class="navbartoptabs"><a href="plan_selftest.php">SELF TEST</a></li>-->
					<!--<li role="presentation" class="navbartoptabs"><a href="plan_labtest.php">LAB TEST</a></li>-->
					<!--<li role="presentation" class="navbartoptabs"><a href="plan_diet.php">DIET</a></li>-->
					<!--<li role="presentation" class="navbartoptabs"><a href="plan_exercise.php">EXERCISE</a></li>-->
					<!--<li role="presentation" class="navbartoptabs"><a href="plan_instruction.php">INSTRUCTION</a></li>-->
				</ul>
			</div>
			    <div style="height:100%;">
    	<div class="col-lg-2 col-md-3 col-sm-3 hidden-xs paddingrl0" style="margin-right:0px;padding-right:0px;" id="activitylist">
    	<div class="navbar navbar-default" role="navigation" style="height: 100%;background-color:#004F35;position:fixed;width:16.7%;">
    		<div id="listBar">
		    	<div id="listHeader">
		    		<h6 style="font-family:Freestyle;font-size:180%;margin-top:-7px;letter-spacing:1px;">Appointments</h6>
		    	</div>
		    	<div id="listItems">
		    		<button type="button" id="addItemButton" class="btns">Add Appointments<img src="images/addItem.png"></button>
		    		<?php 
		    		$get_plan_appointments 		= "select AppointmentDate, CreatedDate from APPOINTMENT_HEADER where PlanCode = '$current_created_plancode'";
		    		//echo $get_plan_appointments;exit;
		    		$get_plan_appointments_run 	= mysql_query($get_plan_appointments);
		    		$get_plan_appointments_count 	= mysql_num_rows($get_plan_appointments_run);
		    		if($get_plan_appointments_count > 0){
			    		while ($get_appo_row = mysql_fetch_array($get_plan_appointments_run)) {
			    			$appointment_date   = date('d-M-Y',strtotime($get_appo_row['AppointmentDate'])); 
			    			$appointment_created_date = date('d-M-Y',strtotime($get_appo_row['CreatedDate'])); 
			    			?>
			    			<button type="button" class="btns editappointmentbuttons" id="<?php echo $appointment_date;?>">
				    			<span><?php echo $appointment_date;?></span><br>
				    		</button>
			    			<?php
			    		}		    			
		    		}
		    		?>
		    	</div>
		    	<!--<div id="listTemplates">
		    		<ul>
		    			<li><a href="#1">Template 1</a></li>
		    			<li><a href="#2">Template 2</a></li>
		    			<li><a href="#3">Template 3</a></li>
		    		</ul>
		    	</div>-->
		    </div>
		    </div>
    	</div>
    	<div class="col-lg-10 col-md-9 col-sm-9 col-sm-12 maincontent">
	    	<div id="dynamicPagePlusActionBar">
	    		<label>
	    			You must first add appointments.  <span id='getappointments'>Click here</span> to start adding appointments or Select A Template to get started.
	    		</label>
	    	</div>
    	</div>
    </div>
		</div>
		<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/ndatepicker-ui.js"></script>
		<script type="text/javascript" src="js/bootstrap-timepicker.js"></script>
		<script type="text/javascript" src="js/modernizr.js"></script>
		<script type="text/javascript" src="js/placeholders.min.js"></script>
		<script type="text/javascript" src="js/bootbox.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			$('#2').addClass('active');
			mainHeader = 115;//include main header height also
			plantitleHeight = $("#plantitle").outerHeight(true);//true includes margin height also
			navigationbarHeight = $("#navigationbar").outerHeight(true);//true includes margin height also
			totalUsedHeight = plantitleHeight + navigationbarHeight + mainHeader;
			listBarHeight = ($(window).innerHeight()-totalUsedHeight)
		  	//$('#listBar').css({height: listBarHeight});
		  	$('#dynamicPagePlusActionBar').css({height: listBarHeight});
			
		    var h = window.innerHeight;
		  	var windowheight = h;
       		var available_height = h - 210;
        	$('.maincontent').height(available_height);

			var appointmentcount = 0;
			$('#plapiper_pagename').html("Appointments");
			$(document).on('focus', '.appoinmentdate', function () {
				$(this).datepicker({
			        dateFormat: "dd-M-yy",
			        minDate: 0,
			        changeMonth: true,
			        changeYear: true,
			     });
			});
			$(document).on('focus', '.appointmenttime', function () {
				$(this).timepicker("show");
			});
			//ON CLICK OF SAVE BUTTON
			$(document).on('click', '#saveAndEdit', function () {
				var numberOfAppointments = 0;
				var current_usedappointmentcount = $('#usedappointmentcount').val();//ID OF ALL THE FORM ROW ELEMENTS PRESENT SEPERATED BY COMMAS. EG: 1,2,3,
				var result = current_usedappointmentcount.split(',');
				appointmentcount = $('#appointmentcount').val(); //TOTAL NUMBER OF APPOINTMENT FIELDS PRESENT CURRENTLY ON THE PAGE
				//bootbox.alert(current_usedappointmentcount);
				for (i = 0; i < appointmentcount; ++i) {
					var appointment_name = $('#appointmentName'+result[i]).val();
					
					if(!appointment_name == ""){
						if(appointment_name.replace(/\s+/g, '') == ""){
					 		bootbox.alert("Appointment name cannot be left blank");
					 		$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#appointmentName'+result[i]).focus();
							});
					 		$('#appointmentName'+result[i]).val("");
							return false;
				 		}
						numberOfAppointments = numberOfAppointments + 1;
						var current_doctor_name = $('#doctorName'+result[i]).val();
						if(current_doctor_name.replace(/\s+/g, '') == ""){
							bootbox.alert("Please enter the doctors name");
							$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#doctorName'+result[i]).focus();
							});
							$('#doctorName'+result[i]).val("");
							return false;
						}
						var appointment_date = $('#appointment_date'+result[i]).val();
						if(appointment_date.replace(/\s+/g, '') == ""){
					 		bootbox.alert("Please select the appointment date");
					 		$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#appointment_date'+result[i]).focus();
							});
					 		$('#appointment_date'+result[i]).val("");
							return false;
				 		}
				 		var appointment_time = $('#appointment_time'+result[i]).val();
						if(appointment_time.replace(/\s+/g, '') == ""){
					 		bootbox.alert("Please select the appointment time");
					 		$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#appointment_time'+result[i]).focus();
							});
					 		$('#appointment_time'+result[i]).val("");
							return false;
				 		}
					}
				}
				if(numberOfAppointments == 0){
					bootbox.alert("Please enter atleast one appointment to continue.");
					$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#appointmentName1').focus();
					});
					return false;
				}
				$('#frm_plan_appointments').submit();
			});

			setTimeout(function() {
		        $("#addItemButton").trigger('click');
		    },1);


			$('#addItemButton, #getappointments').click(function(){
				if(appointmentcount > 0){
					var discard = confirm("The current appointments will be discarded. Click OK to continue.");
					if(discard == true){
						appointmentcount = 0;
					} else {

					}
				} else {
					appointmentcount = 0;
				}
				//bootbox.alert(appointmentcount);
				if(appointmentcount == 0){
					$.ajax({
						type        : "GET",
						url			: "appointmentsDefaultPage.php",
						dataType	: "html",
						success	: function (response)
						{ 
							$('#dynamicPagePlusActionBar').html(response);
							appointmentcount = 3;
						 },
						 error: function(error)
						 {
						 	//bootbox.alert(error);
						 }
					}); 					
				}
		
			});
			//FINISHED ADDING
			$('#finished_adding').click(function(){
				window.location.href = "finishedadding.php";
			});
			//EDIT APPOINTMENT BUTTON CLICKED - FROM SIDE PANEL
			$('.editappointmentbuttons').click(function(){
			var appoid = $(this).attr('id');
				//bootbox.alert(appoid);
			window.location.href = "edit_plan_appointments.php?id="+appoid;
			});

		var sidebarflag = 0;
        $('#topbar-leftmenu').click(function(){
	      if(sidebarflag == 1){
              $('#sidebargrid').hide("slow","swing");
              $('#activitylist').show("slow","swing");
              $('.maincontent').addClass("col-lg-10");
              sidebarflag = 0;
          } else {
              $('#sidebargrid').show("slow","swing");
              $('#activitylist').hide("slow","swing");
              $('.maincontent').removeClass("col-lg-10");
              $('.maincontent').removeClass("col-md-9");
              $('.maincontent').removeClass("col-sm-9");
              sidebarflag = 1;
          }
        });
                var merchant = '<?php echo $logged_merchantid;?>';
        <?php include('js/notification.js');?>
		});
		</script>

    </body>
    <?php
	include('include/unset_session.php');
	?>
</html>