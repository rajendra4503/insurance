<?php 
session_start();
//ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php'); 
$plan_to_customize = $_SESSION['current_assigned_plan_code'];
$assigned_to_user  = $_SESSION['current_assigned_user_id'];
$plan_to_customize_name = $_SESSION['current_assigned_plan_name'];
//echo $plan_to_customize;exit;
if((isset($_REQUEST['id'])) && (!empty($_REQUEST['id']))){
	$appo_edit_id = $_REQUEST['id'];
	$appo_edit_id =  date('Y-m-d',strtotime($appo_edit_id));
	//echo $appo_edit_id;exit;
} else {
	//$appo_edit_id = "1";
	$get_plan_appointments 		= "select AppointmentDate, CreatedDate from USER_APPOINTMENT_HEADER where PlanCode = '$plan_to_customize' and UserID='$assigned_to_user'";
		    		//echo $get_plan_appointments;exit;
		    		$get_plan_appointments_run 	= mysql_query($get_plan_appointments);
		    		$get_plan_appointments_count 	= mysql_num_rows($get_plan_appointments_run);
		    		if($get_plan_appointments_count > 0){
			    		while ($get_appo_row = mysql_fetch_array($get_plan_appointments_run)) {
			    			$appo_edit_id   = $get_appo_row['AppointmentDate']; 
			    		}		    			
		    		}
}
//echo $appo_edit_id;exit;
if((isset($_REQUEST['hidden_value']))&&(!empty($_REQUEST['hidden_value']))){
	//echo "<pre>";print_r($_REQUEST);exit;
	//DELETE ALL EXISTING APPOINTMENTS
	$appointmentcount    		= mysql_real_escape_string(trim($_REQUEST['appointmentcount'])); //Total number of appointments present on screen
		if($appointmentcount > '0'){
	$delete_user_header = mysql_query("delete from USER_APPOINTMENT_HEADER where  PlanCode = '$plan_to_customize' and UserID='$assigned_to_user'");
	$delete_user_details = mysql_query("delete from USER_APPOINTMENT_DETAILS where  PlanCode = '$plan_to_customize' and UserID='$assigned_to_user'");	
	}
	$usedappointmentcount 		= mysql_real_escape_string(trim($_REQUEST['usedappointmentcount'])); //row ids of appointments present
	$appointmentids 			= explode(",", $usedappointmentcount);
	$datearray = array();
	$get_previous_dates = mysql_query("select AppointmentDate from USER_APPOINTMENT_HEADER where PlanCode = '$plan_to_customize' and UserID='$assigned_to_user'");
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
			$insert_appointment_details = "insert into USER_APPOINTMENT_DETAILS (`UserID`,`PlanCode`, `AppointmentDate`, `AppointmentTime`, `AppointmentShortName`, `DoctorsName`, `AppointmentRequirements`, `CreatedDate`, `CreatedBy`) values ('$assigned_to_user','$plan_to_customize', '$appointment_date', '$appointment_time', '$appointment_name','$doctor_name','$appointment_reqs', now(), '$logged_userid');";
			//echo $insert_appointment_details;exit;
			$insert_appointment_run  	= mysql_query($insert_appointment_details);
		}
	}
	}
	//echo "<pre>";print_r($datearray);exit;
	foreach ($datearray as $date) {
		$insert_appointment_header = "insert into USER_APPOINTMENT_HEADER (`UserID`,`PlanCode`, `AppointmentDate`, `CreatedDate`, `CreatedBy`) values ('$assigned_to_user','$plan_to_customize', '$date', now(), '$logged_userid')";
			//echo $insert_appointment_header;exit;
			$insert_header_run  	= mysql_query($insert_appointment_header);
	}
		$rand = mt_rand(1,999);
  $update_header = mysql_query("update USER_PLAN_HEADER set PlanStatus = '$rand' where PlanCode='$plan_to_customize' and UserID = '$assigned_to_user'");
		header("Location:customize_appointments.php");
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper | Customize Appointments</title>
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
		<div id="planpiper_wrapper" class="fullheight">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
			<?php include_once('top_header.php');?>
			</div>

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 paddingrl0">
				<div id="plantitle"><?php echo $plan_to_customize_name;?>
<button type="button" class="btns" align="right" id="finished_adding"><img src="images/finishAdd.png" style="height:20px;width:auto;margin-bottom:3px;">&nbsp;FINISH CUSTOMIZING</button></div>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<ul class="nav nav-pills nav-justified">
				<?php
				echo $modules;
				?>
					<!--<li role="presentation" class="navbartoptabs"><a href="customize_medication.php">MEDICATION</a></li>
					<li role="presentation" class="active navbartoptabs"><a href="customize_appointments.php">APPOINTMENT</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_selftest.php">SELF TEST</a></li
					<li role="presentation" class="navbartoptabs"><a href="customize_labtest.php">LAB TEST</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_diet.php">DIET</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_exercise.php">EXERCISE</a></li>
					<li role="presentation" class="navbartoptabs"><a href="customize_instruction.php">INSTRUCTION</a></li>>-->
				</ul>
			</div>
			    <div  style="height:100%;">
    	<div class="col-lg-2 col-md-3 col-sm-3 hidden-xs paddingrl0" style="margin-right:0px;padding-right:0px;" id="activitylist">
    	<div class="navbar navbar-default" role="navigation" style="height: 100%;background-color:#004F35;position:fixed;width:16.7%;">
    		<div id="listBar">
		    	<div id="listHeader">
		    		<h6 style="font-family:Freestyle;font-size:180%;margin-top:-7px;letter-spacing:1px;">Appointment List</h6>
		    	</div>
		    	<div id="listItems">
		    		<button type="button" id="addItemButton" class="btns"  style="display:none;">Add Appointments<img src="images/addItem.png"></button>
		    		<?php 
		    		$get_plan_appointments 		= "select AppointmentDate, CreatedDate from USER_APPOINTMENT_HEADER where PlanCode = '$plan_to_customize' and UserID='$assigned_to_user'";
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
	    		<form name="frm_customize_appointments" id="frm_customize_appointments" method="post" action="">
	    		<span style="float:left;width:3%;">
			        <table id="aslno">

			        </table>
			      </span>
			 <div class="table-responsive" style="float:left;width:95%;padding-bottom:100px;">
			      <table id="adata" class="table table-striped">
			      <tr id="aheader">
			          <th>Appointment short name</th>
			          <th style="width:180px;">Doctor's Name</th>
			          <th style="width:130px;">Date</th>
			          <th style="width:100px;">Time</th>
			          <th>Requirements</th>
			          <th></th>
			        </tr>
			        <?php 
			        	if(isset($appo_edit_id)){
			        		$appo_count = 0;
				   			$appo_count_string = "";
				   		$get_appodetails_to_edit = mysql_query("select `PlanCode`, `AppointmentDate`, `AppointmentTime`, `AppointmentShortName`, `DoctorsName`, `AppointmentRequirements` from USER_APPOINTMENT_DETAILS where PlanCode = '$plan_to_customize' and UserID='$assigned_to_user'");
				   		//echo $get_appodetails_to_edit;exit;
				   		$appodetails_num = mysql_num_rows($get_appodetails_to_edit);
				   		if($appodetails_num > 0){
				   			while ($detailsrow = mysql_fetch_array($get_appodetails_to_edit)) {
				   				$appo_count++;
				   				$appo_count_string 			.= $appo_count.",";
				   				$appoinmentdate1 			=  date('d-M-Y',strtotime($detailsrow['AppointmentDate'])); 
				   				$appointmenttime1 			= $detailsrow['AppointmentTime'];
				   				$appointmentname1 			= $detailsrow['AppointmentShortName'];
				   				$doctorsname1				= $detailsrow['DoctorsName'];
				   				$requirements1 				= $detailsrow['AppointmentRequirements'];
				   			?>

			         <tr>
				          <td><input type="text" name="appointmentName<?php echo $appo_count;?>" placeholder="Enter Appointment Name.." maxlength="25" id="appointmentName<?php echo $appo_count;?>" class="forminputs2" value="<?php echo $appointmentname1;?>"></td>
				          <td><input type="text" name="doctorName<?php echo $appo_count;?>" placeholder="Enter Doctor Name.." maxlength="25" id="doctorName<?php echo $appo_count;?>" class="forminputs2" value="<?php echo $doctorsname1;?>"></td>
				          <td><input type="text" name="appointment_date<?php echo $appo_count;?>" placeholder="Date" maxlength="25" id="appointment_date<?php echo $appo_count;?>" readonly class="forminputs2 appoinmentdate" value="<?php echo $appoinmentdate1;?>"></td>
				          <td><div class="input-append bootstrap-timepicker"><span class="add-on"> <input type="text" name="appointment_time<?php echo $appo_count;?>" placeholder="Time" maxlength="25" id="appointment_time<?php echo $appo_count;?>" readonly class="forminputs2 appointmenttime" value="<?php echo $appointmenttime1;?>"></span></div></td>
				          <td><textarea name="requirements<?php echo $appo_count;?>" id='requirements<?php echo $appo_count;?>' placeholder="Enter Requirements for the appointment.." class="forminputs2" value=""><?php echo $requirements1;?></textarea></td>
				          <td><img src="images/closeRow.png" width="25px" height="auto" class="deleterow" id="<?php echo $appo_count;?>"></td>
				        </tr>
				        <?php
				    }
				    ?>
				        </table>
				        </div>
				              <input type="hidden" name="usedappointmentcount" id="usedappointmentcount" value="<?php echo $appo_count_string;?>">
						      <input type="hidden" name="existingappointmentcount" id="existingappointmentcount" value="<?php echo $appo_count;?>">
						      <input type="hidden" name="appointmentcount" id="appointmentcount" value="<?php echo $appo_count;?>">
						      <input type="hidden" name="hidden_value" id="hidden_value" value="Y">				    
				    <?php
				   		}
			        	}
			        ?>

		    	</form>
	    	</div>
    	</div>
        <div id="ActionBar" class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="position: fixed;bottom: 0;background-color: #fff;text-align: center;">
         <button type="button" id="addappointment" class="btns formbuttons">ADD AN APPOINTMENT</button>
        <button type="button" id="saveAndEdit" class="btns formbuttons">SAVE</button>
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
			listBarHeight = ($(window).innerHeight()-totalUsedHeight);
		  	//$('#listBar').css({height: listBarHeight});
		  	$('#dynamicPagePlusActionBar').css({height: listBarHeight});
		  	
			var h = window.innerHeight;
		  	var windowheight = h;
       		var available_height = h - 210;
        	$('.maincontent').height(available_height);

		  	var appointmentcount = <?php echo $appo_count;?>;
			var propercount 	= <?php echo $appo_count;?>;
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
			$('#addappointment').click(function(){
		        appointmentcount = appointmentcount + 1;
		        propercount = propercount + 1;
		        $('.deleterow').show();
		        var first = "<tr><td><input type='text' name='appointmentName"+propercount+"' placeholder='Enter Appointment Name..' maxlength='25' id='appointmentName"+propercount+"' class='forminputs2'></td><td><input type='text' name='doctorName"+propercount+"' placeholder='Enter Doctor Name..' size='25px' maxlength='25' id='doctorName"+propercount+"' class='forminputs2'></td><td><input type='text' name='appointment_date"+propercount+"' placeholder='Date' maxlength='25' id='appointment_date"+propercount+"' readonly class='forminputs2 appoinmentdate'></td><td><input type='text' name='appointment_time"+propercount+"' placeholder='Time' maxlength='25' id='appointment_time"+propercount+"' readonly class='forminputs2 appointmenttime'></td><td><textarea name='requirements"+propercount+"' id='requirements"+propercount+"' placeholder='Enter Requirements for the appointment..' class='forminputs2'></textarea></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"'></td></tr>";
		        var slno  = "<tr><td>"+appointmentcount+"</td></tr>";
		        $('#aslno > tbody').append(slno);
		        $('#adata > tbody').append(first);
		        var current_usedappointmentcount = $('#usedappointmentcount').val();
		        var new_usedappointmentcount = current_usedappointmentcount+propercount+",";
		        $('#usedappointmentcount').val(new_usedappointmentcount);
		        $('#appointmentcount').val(appointmentcount);
	      });
		  $(document).on('click', '.deleterow', function () {
		    var deleted_row_id = $(this).attr('id');  
		    //bootbox.alert(deleted_row_id);
		    var appointment_name = $('#appointmentName'+deleted_row_id).val();
		   if(appointment_name.replace(/\s+/g, '') == ""){
		      $('#aslno tr:last').remove();
		      //this.parentNode.parentNode.remove();
		      $(this).closest('tr').remove();
		      appointmentcount = appointmentcount - 1;
		      if(appointmentcount == 1){
		        $('.deleterow').hide();
		      }
		        var deleted_usedappointment = deleted_row_id+",";
		        var current_usedappointmentcount = $('#usedappointmentcount').val();
		        var new_usedappointmentcount  = current_usedappointmentcount.replace(deleted_usedappointment, "");
		        $('#usedappointmentcount').val(new_usedappointmentcount);
		        $('#appointmentcount').val(appointmentcount);
		   } else {
		    var deleteconfirm = confirm("This appointment will be deleted. Click OK to continue.");
		    if(deleteconfirm == true){
		       $('#aslno tr:last').remove();
		      //this.parentNode.parentNode.remove();
		      $(this).closest('tr').remove();
		      appointmentcount = appointmentcount - 1;
		      if(appointmentcount == 1){
		        $('.deleterow').hide();
		      }
		        var deleted_usedappointment = deleted_row_id+",";
		        var current_usedappointmentcount = $('#usedappointmentcount').val();
		        var new_usedappointmentcount  = current_usedappointmentcount.replace(deleted_usedappointment, "");
		        $('#usedappointmentcount').val(new_usedappointmentcount);
		        $('#appointmentcount').val(appointmentcount);
		    } else {

		    }
		   }
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
				$('#frm_customize_appointments').submit();
			});
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
				window.location.href = "customize_plan.php";
			});
			//EDIT APPOINTMENT BUTTON CLICKED - FROM SIDE PANEL
			$('.editappointmentbuttons').click(function(){
			var appoid = $(this).attr('id');
				//bootbox.alert(appoid);
			window.location.href = "customize_appointments.php?id="+appoid;
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

		});
		</script>
    </body>
    <?php
	include('include/unset_session.php');
	?>
</html>