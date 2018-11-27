<?php
session_start();
//echo "<pre>";print_r($_SESSION);exit;
//ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php');
//$_SESSION['plancode_for_current_plan'] 	= "IN0000000022";
$plancode_for_current_plan 				= $_SESSION['plancode_for_current_plan'];
$planname_for_current_plan_text 		= "Click to edit Plan Details";
$planname_for_current_plan = "";
$plandesc_for_current_plan = "";
if(isset($_SESSION['plancode_for_current_plan'])){
	$plancode_for_current_plan = $_SESSION['plancode_for_current_plan'];
	//Get Plan Details
		if($plancode_for_current_plan != ""){
		$get_plan_details = "select PlanCode, PlanName, PlanDescription from PLAN_HEADER where PlanCode = '$plancode_for_current_plan'";
	//echo $get_plan_details;exit;
	$get_plan_details_run = mysql_query($get_plan_details);
	$get_plan_details_count = mysql_num_rows($get_plan_details_run);
			 		if($get_plan_details_count > 0){
			 			while ($plan_details = mysql_fetch_array($get_plan_details_run)) {
			 				$plancode_for_current_plan 			= $plan_details['PlanCode'];
			 				$planname_for_current_plan 			= $plan_details['PlanName'];
			 				$planname_for_current_plan_text     = $plan_details['PlanName'];
			 				$plandesc_for_current_plan 			= $plan_details['PlanDescription'];
			 				}
			 		} else {

			 		}
	}
}

$plan_to_customize="";
if((isset($_REQUEST['hidden_value']))&&(!empty($_REQUEST['hidden_value']))){
	//echo "<pre>";print_r($_REQUEST);exit;
	//DELETE ALL EXISTING APPOINTMENTS
	$appointmentcount    		= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['appointmentcount']))); //Total number of appointments present on screen
		if($appointmentcount > '0'){
	$delete_user_header = mysql_query("delete from PLAN_PROCEDURE where  PlanCode = '$plancode_for_current_plan'");
		
	}

	$usedappointmentcount 		= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['usedappointmentcount']))); //row ids of appointments present
	$appointmentids 			= explode(",", $usedappointmentcount);
	$datearray = array();

	/*$get_previous_dates = mysql_query("select AppointmentDate from USER_APPOINTMENT_HEADER where PlanCode = '$plancode_for_current_plan' and UserID='$assigned_to_user'");
	$date_count 				= mysql_num_rows($get_previous_dates);
	if($date_count > 0){
		while($datesentered 		= mysql_fetch_array($get_previous_dates)){
			$datepresent 			= $datesentered['AppointmentDate'];
			 array_push($datearray, $datepresent);
		} 		
	}*/
	//echo "<pre>";print_r($datearray);exit;
	foreach ($appointmentids as $ids) {
		if($ids != ""){
			$appointment_name =  mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["procedure$ids"])));

			if($appointment_name != ""){
			$appointment_date =  mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["procedure_date$ids"])));
			$appointment_date =  date('Y-m-d',strtotime($appointment_date));
			if (!in_array($appointment_date, $datearray)){
				  array_push($datearray, $appointment_date);
				  }
		
			$insert_appointment_details = "insert into PLAN_PROCEDURE (`PlanCode`, `ProId`, `ProDate`,`CreatedDate`, `CreatedBy`) values ('$plancode_for_current_plan', '$appointment_name', '$appointment_date', now(), '$logged_userid');";
			//echo $insert_appointment_details;exit;
			$insert_appointment_run  	= mysql_query($insert_appointment_details);
		}
	}
	}
	//echo "<pre>";print_r($datearray);exit;
	/*foreach ($datearray as $date) {
		$insert_appointment_header = "insert into APPOINTMENT_HEADER (`PlanCode`, `AppointmentDate`, `CreatedDate`, `CreatedBy`) values ('$plancode_for_current_plan', '$date', now(), '$logged_userid')";
			//echo $insert_appointment_header;exit;
			$insert_header_run  	= mysql_query($insert_appointment_header);
	}*/
		header("Location:plan_procedure.php");
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Plan Piper | Procedure</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/ndatepicker.css">
		<link rel="stylesheet" type="text/css" href="css/bootstrap-timepicker.min.css">
		<link rel="stylesheet" type="text/css" href="css/planpiper.css">
		<link rel="stylesheet" type="text/css" href="fonts/font.css">
		<link rel="shortcut icon" href="images/planpipe_logo.png"/>
        <script type="text/javascript">
		    var imageflag = 0;
		    var flag = 0;
		    var flag2 = 0;
		      function changeimage(id){
		        imageflag = "arrow"+id;
		        if(flag != 0){
		          if(imageflag == flag) {
		          document.getElementById(imageflag).src = "images/rightarrow.png";
		          flag2 = 1;
		        } else if(imageflag != flag){
		           document.getElementById(flag).src = "images/rightarrow.png";
		           document.getElementById(imageflag).src = "images/downarrow.png";
		            } 
		      } else {
		        document.getElementById(imageflag).src = "images/downarrow.png";
		      }
		      if(flag2 != 1){
		        flag = "arrow"+id;
		      } else {
		        flag = 0;
		      }
		      flag2 = 0;
		      }
        </script>
        <style type="text/css">
	        #pdata td select, #adata td select {
	        	background-color: #fff;
			    color: #000;
			    height: 40px;
			    padding: 0px;
			    -webkit-appearance: menulist-button;
			    border: 1px solid #000;
			    border-radius: 0px;
			}
        </style>
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
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="left"  id="plantitle"></div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="center"  id="plantitle"><span id="thisplantitle" title="Click to edit the plan details"><?php echo $planname_for_current_plan_text;?></span><span title="Click to edit the plan details" id="editthisplantitle">&nbsp;&nbsp;<img src="images/editad.png" style="height:20px;cursor:pointer;"></span></div>
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="right"  id="plantitle"><button type="button" class="btns" align="right" id="finished_adding"><img src="images/finishAdd.png" style="height:20px;width:auto;margin-bottom:3px;">&nbsp;FINISH ADDING</button></div>
</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<ul class="nav nav-pills nav-justified">
					<?php
					echo $modules;
					?>
				</ul>
			</div>
			    <div  style="height:100%;">
    	<div class="col-lg-2 col-md-3 col-sm-3 hidden-xs paddingrl0" style="margin-right:0px;padding-right:0px;" id="activitylist">
    	<div class="navbar navbar-default" role="navigation" style="height: 100%;background-color:#004F35;position:fixed;width:16.7%;">
    		<div id="listBar" align="center">
    		<h6 style="font-family:Freestyle;font-size:33px;margin-top:-1px;letter-spacing:1px;color:#f2bd43;background-color:#000;">Procedure List</h6>
    		<!-- <div class="sidebarheadings">Master Appointments :</div>
    			<div class="panel-group masterplanactivities" id="accordion1" role="tablist" aria-multiselectable="true" style="max-height:250px;overflow:scroll;overflow-x:hidden;">
			    		<?php
			    		$get_plan_appointments 		= "select  t1.PlanCode, t1.AppointmentShortName, t1.DoctorsName,
			    		t1.AppointmentDate,t1.AppointmentTime from APPOINTMENT_DETAILS as t1,PLAN_HEADER as t2 
			    		where t1.PlanCode=t2.PlanCode and t2.MerchantID='$logged_merchantid' and t2.PlanStatus='A' ";
		    		$get_plan_appointments_run 	= mysql_query($get_plan_appointments);
		    		$get_plan_appointments_count 	= mysql_num_rows($get_plan_appointments_run);
		    		if($get_plan_appointments_count > 0){
			    		while ($get_presc_row 	= mysql_fetch_array($get_plan_appointments_run)) {
			    			$appointment_time 	= date('His',strtotime($get_presc_row['AppointmentTime']));
			    			$appointment_name 	= $get_presc_row['AppointmentShortName'];
			    			$fortitle			= $get_presc_row['AppointmentShortName'];
			    			$length 			= strlen($appointment_name);
			    			if($length > 12){
			    				$appointment_name 	= substr($appointment_name,0,12);
			    				$appointment_name 	= $appointment_name."...";
			    			}
			    			$appointment_doc  	= $get_presc_row['DoctorsName'];
			    			$appointment_code 	= $get_presc_row['PlanCode'];
			    			$appointment_date 	= date('dMY',strtotime($get_presc_row['AppointmentDate']));
			    			$appointment_date2 	= date('d-M-Y',strtotime($get_presc_row['AppointmentDate']));
			    			?>
			    			 <div class="panel panel-default plancreationpanel">
					              <div class="panel-heading plancreationaccordion" role="tab" id="heading<?php echo $appointment_code.$appointment_time.$appointment_date; ?>">
					                <h4 class="panel-title" style="text-align:center;">
					                  <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $appointment_code.$appointment_time.$appointment_date; ?>" aria-expanded="false" aria-controls="collapse"><img src="images/rightarrow.png" style="height:12px;width:auto;" align="left" id="arrow<?php echo $appointment_code.$appointment_time.$appointment_date; ?>" onclick='changeimage("<?php echo $appointment_code.$appointment_time.$appointment_date; ?>");'></a>
					                   <span title='<?php echo $fortitle;?>'><?php echo $appointment_name;?></span>
					                  <img src="images/addtoright.png" class="addmasterplanappointments" align="right" style="height:20px;width:auto;cursor:pointer;background-color:#f2bd43;" id="<?php echo $appointment_code."~~".$appointment_time."~~".$appointment_date; ?>" title="Add to current plan">
					                </h4>
					              </div>
					              <div id="collapse<?php echo $appointment_code.$appointment_time.$appointment_date; ?>" class="panel-collapse collapse plancreationpanelbody" role="tabpanel" aria-labelledby="heading<?php echo $appointment_code.$appointment_time.$appointment_date; ?>">
					                <div class="panel-body"  style="text-align:center;">
					                	<?php
					                		echo $appointment_doc."<br>".$appointment_date2;
					                	?>
					                </div>
					              </div>
					            </div>
			    			<?php
			    		}
		    		} else {
		    			echo "<div style='color:#fff;'>No Appointments to show</div>";
		    		}
			    		?>
		    		</div>
		    		<div class="sidebarheadings" style="margin-top: -13px;">Assigned Appointments :</div>
		    				    		<div class="panel-group assignedplanactivities" id="accordion2" role="tablist" aria-multiselectable="true" style="overflow:scroll;overflow-x:hidden;">
			    		<?php
			    		$get_plan_appointments 		= "select  t1.UserID, t1.PlanCode, t1.AppointmentShortName,
			    		t1.DoctorsName, t1.AppointmentDate, t1.AppointmentTime from USER_APPOINTMENT_DETAILS as t1,USER_PLAN_HEADER as t2 
			    		where t1.PlanCode=t2.PlanCode and t1.UserID=t2.UserID and t2.MerchantID='$logged_merchantid'";
		    		$get_plan_appointments_run 	= mysql_query($get_plan_appointments);
		    		$get_plan_appointments_count 	= mysql_num_rows($get_plan_appointments_run);
		    		if($get_plan_appointments_count > 0){
			    		while ($get_presc_row = mysql_fetch_array($get_plan_appointments_run)) {
			    			$appointment_time   = date('His',strtotime($get_presc_row['AppointmentTime']));
			    			$appointment_name 	= $get_presc_row['AppointmentShortName'];
			    			$fortitle			= $get_presc_row['AppointmentShortName'];
			    			$length 			= strlen($appointment_name);
			    			if($length > 12){
			    				$appointment_name 	= substr($appointment_name,0,12);
			    				$appointment_name 	= $appointment_name."...";
			    			}
			    			$appointment_doc  	= $get_presc_row['DoctorsName'];
			    			$appointment_code  	= $get_presc_row['PlanCode'];
			    			$appointment_user  	= $get_presc_row['UserID'];
			    			$appointment_date 	= date('dMY',strtotime($get_presc_row['AppointmentDate']));
			    			$appointment_date2 	= date('d-M-Y',strtotime($get_presc_row['AppointmentDate']));
			    			?>
			    			 <div class="panel panel-default plancreationpanel">
					              <div class="panel-heading plancreationaccordion" role="tab" id="heading<?php echo $appointment_user.$appointment_code.$appointment_time.$appointment_date; ?>">
					                <h4 class="panel-title" style="text-align:center;">
					                  <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $appointment_user.$appointment_code.$appointment_time.$appointment_date; ?>" aria-expanded="true" aria-controls="collapse<?php echo $appointment_user.$appointment_code.$appointment_time.$appointment_date; ?>"><img src="images/rightarrow.png" style="height:12px;width:auto;cursor:pointer;" align="left"></a>
					                   <span title='<?php echo $fortitle;?>'><?php echo $appointment_name;?></span>
					                  <img src="images/addtoright.png" class="addassignedplanappointments" align="right" style="height:20px;width:auto;cursor:pointer;background-color:#f2bd43;" id="<?php echo $appointment_user."~~".$appointment_code."~~".$appointment_time."~~".$appointment_date; ?>" title="Add to current plan">
					                </h4>
					              </div>
					              <div id="collapse<?php echo $appointment_user.$appointment_code.$appointment_time.$appointment_date; ?>" class="panel-collapse collapse plancreationpanelbody" role="tabpanel" aria-labelledby="heading<?php echo $appointment_user.$appointment_code.$appointment_time.$appointment_date; ?>">
					                <div class="panel-body" style="text-align:center;">
					                	<?php
					                		echo $appointment_doc."<br>".$appointment_date2;
					                	?>
					                </div>
					              </div>
					            </div>
			    			<?php
			    		}
		    		} else {
		    			echo "<div style='color:#fff;'>No Appointments to show</div>";
		    		}
			    		?>
		    		</div> -->
		    </div>
		    </div>
    	</div>
	    	<div class="col-lg-10 col-md-9 col-sm-9 col-sm-12 maincontent">
	    	<div id="dynamicPagePlusActionBar">
	    		<label>
	    			You must first add appointments. <span id='getappointments'>Click here</span> to start adding appointments or Select A Template to get started.
	    		</label>
	    	</div>
    </div>
		</div>
         <!--SHOW PLAN DETAILS MODAL WINDOW-->
            <div class="modal" id="plandetailsmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header padding0" align="center" style="border:none;">
                   <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h5 class="modal-title" id="modalheadings">Enter Plan Details</h5>
                  </div>
                  	<div align="left" style="padding-left:5px;font-family:RalewayRegular;color:#000;">
						<input type="text" placeholder="Enter the Plan Title here" name="plan_name" id="plan_name" class="firstlettercaps" title="Plan Title" onkeypress='keychk(event)' maxlength="50" style="width:100%;" value="<?php echo $planname_for_current_plan;?>">
                        <textarea placeholder="Type the plan description here" id="plan_desc" name="plan_desc" title="Plan Description" rows="4" style="resize:none;border-bottom:1px solid #004f35;"  maxlength="499"><?php echo $plandesc_for_current_plan;?></textarea>
                        <!--ADDED-->
                        <div id="textarea_feedback" style="color:#004F35;font-family:Raleway;padding-bottom:10px;text-align:right"></div>
                        <!---->
                        <!--<span>Upload a Cover Image (Optional):  <input id="plan_cover_image" name="plan_cover_image" type="file" accept='image/*' style="display:inline;"></span>-->
					</div>
               	  <div class="margin10" align="center">
               	  <input type="hidden" name="plancode_for_current_plan" id="plancode_for_current_plan" value="<?php echo $plancode_for_current_plan;?>">
               	  <button class="smallbutton" id="plandetailsentered">Done</button>
               	  </div>
              	</div>
              </div>
            </div>
    <!--END OF PLAN DETAILS MODAL WINDOW-->
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
			$('#12').addClass('active');
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

        	var sidelistheight = $('#listBar').height();
        	var availableheight = sidelistheight - 280;
        	availableheight = availableheight/2;
        	//alert(availableheight);
        	$('.masterplanactivities').height(availableheight);
        	$('.assignedplanactivities').height(availableheight);

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
			$('#thisplantitle').click(function(){
				$('#plandetailsmodal').modal('show');
			});
			$('#editthisplantitle').click(function(){
				$('#plandetailsmodal').modal('show');
			});
			$(document).on('focus', '.appointmenttime', function () {
				$(this).timepicker("show");
			});
			//ON CLICK OF SAVE BUTTON
			$(document).on('click', '#saveAndEdit', function () {
				var plan_name = $('#plan_name').val();
			    	if(plan_name.replace(/\s+/g, '') == ""){
						bootbox.alert("Please enter a title for this plan.");
						$('.bootbox').on('hidden.bs.modal', function() {
						    $('#plan_name').focus();
						});
						$('#plandetailsmodal').modal('show');
						$('#plan_name').val("");
						return false;
					}
				var numberOfAppointments = 0;
				var current_usedappointmentcount = $('#usedappointmentcount').val();//ID OF ALL THE FORM ROW ELEMENTS PRESENT SEPERATED BY COMMAS. EG: 1,2,3,
				var result = current_usedappointmentcount.split(',');
				appointmentcount = $('#appointmentcount').val(); //TOTAL NUMBER OF APPOINTMENT FIELDS PRESENT CURRENTLY ON THE PAGE
				//bootbox.alert(current_usedappointmentcount);
				for (i = 0; i < appointmentcount; ++i) {
					var appointment_name = $('#procedure'+result[i]).val();
					
					if(!appointment_name == ""){
						if(appointment_name.replace(/\s+/g, '') == ""){
					 		bootbox.alert("Procedure cannot be left blank");
					 		$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#procedure'+result[i]).focus();
							});
					 		$('#procedure'+result[i]).val("");
							return false;
				 		}
						numberOfAppointments = numberOfAppointments + 1;
						/*var current_doctor_name = $('#doctorName'+result[i]).val();
						if(current_doctor_name.replace(/\s+/g, '') == ""){
							bootbox.alert("Please enter the doctors name");
							$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#doctorName'+result[i]).focus();
							});
							$('#doctorName'+result[i]).val("");
							return false;
						}*/
						var appointment_date = $('#procedure_date'+result[i]).val();
						if(appointment_date.replace(/\s+/g, '') == ""){
					 		bootbox.alert("Please select the procedure date");
					 		$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#procedure_date'+result[i]).focus();
							});
					 		$('#procedure_date'+result[i]).val("");
							return false;
				 		}
				 		//var dt = new Date();
						//var time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
						//alert(time);
				 		/*var appointment_time = $('#appointment_time'+result[i]).val();
						if(appointment_time.replace(/\s+/g, '') == ""){
					 		bootbox.alert("Please select the appointment time");
					 		$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#appointment_time'+result[i]).focus();
							});
					 		$('#appointment_time'+result[i]).val("");
							return false;
				 		}*/
				 		/*var appointment_dih = $('#duration_inhours'+result[i]).val();
				 		//alert(appointment_dih);
				 		var appointment_dim = $('#duration_inmins'+result[i]).val();
						if((appointment_dih == "00") && (appointment_dim == "00")){
					 		bootbox.alert("Please select the appointment duration");
					 		$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#duration_inhours'+result[i]).focus();
							});
							return false;
				 		}*/
				 		/*var appointment_venue = $('#venue'+result[i]).val();
						if(appointment_venue.replace(/\s+/g, '') == ""){
					 		bootbox.alert("Please enter the venue details");
					 		$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#venue'+result[i]).focus();
							});
					 		$('#venue'+result[i]).val("");
							return false;
				 		}*/
					}
				}
				if(numberOfAppointments == 0){
					bootbox.alert("Please select  atleast one procedure to continue.");
					$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#procedure1').focus();
					});
					return false;
				}
				$('#frm_plan_appointments').submit();
			});

			setTimeout(function() {
		        $("#getappointments").trigger('click');
		        var plan_name = $('#plan_name').val();
		        if(plan_name.replace(/\s+/g, '') == ""){
		        	$('#plandetailsmodal').modal('show');
		        }
		    },1);
			$('#plandetailsentered').click(function(){
		    	var plan_name = $('#plan_name').val();
		    	if(plan_name.replace(/\s+/g, '') == ""){
					bootbox.alert("Please enter a title for this plan.");
					$('.bootbox').on('hidden.bs.modal', function() {
					    $('#plan_name').focus();
					});
					$('#plan_name').val("");
					return false;
				}
				var plan_code = $('#plancode_for_current_plan').val();
				var plan_desc = $('#plan_desc').val();

					var dataString = "type=insert_plan_header&title="+plan_name+"&desc="+plan_desc+"&code="+plan_code+"&mer="+<?php echo $logged_merchantid;?>+"&user="+<?php echo $logged_userid;?>;
					//bootbox.alert(dataString);
					$('#thisplantitle').html(plan_name);
		        	$.ajax({
						type		: 'POST',
						url			: 'ajax_validation.php',
						crossDomain	: true,
						data		: dataString,
						dataType	: 'json',
						async		: false,
						success	: function (response)
							{
								if(response.success == true){
									$('#plandetailsmodal').modal('hide');
								} else {
									$('#plandetailsmodal').modal('hide');
								}
							},
						error: function(error)
						{

						}
					});
		    });
			$('.addmasterplanappointments').click(function(){
				var appo_id = $(this).attr('id');  
			   $("input").each(function() {
			    var id = $(this).attr("id");//id of the current textarea
			    var deleted_row_id  = id.replace("appointmentName", "");
			    if (id.indexOf("appointmentName") >= 0){

			    var appointment_name = $(this).val();
			   	if(appointment_name.replace(/\s+/g, '') == ""){
			      $('#aslno tr:last').remove();
			      //this.parentNode.parentNode.remove();
			      $(this).closest('tr').remove();
			     //alert(appointmentcount);
			     var appointmentcount = $('#appointmentcount').val();
			      appointmentcount = parseInt(appointmentcount) - 1;
			     // alert(appointmentcount);
			      if(appointmentcount == 1){
			        $('.deleterow').hide();
			      }
			        var deleted_usedappointment 		= deleted_row_id+",";
			        var current_usedappointmentcount 	= $('#usedappointmentcount').val();
			        var new_usedappointmentcount  		= current_usedappointmentcount.replace(deleted_usedappointment, "");
			        $('#usedappointmentcount').val(new_usedappointmentcount);
			        $('#appointmentcount').val(appointmentcount);
			    }
			    }
			});
			var master 		= appo_id.split("~~");
			var plancode 	= master[0];
			var appotime 	= master[1];
			var appodate 	= master[2];
			var dataString 	= "plancode="+plancode+"&type=get_master_appointments&time="+appotime+"&date="+appodate;
				//bootbox.alert(dataString);
				$.ajax({
                  type    :"GET",
                  url     :"ajax_validation.php",
                  data    :dataString,
                  dataType  :"jsonp",
                  jsonp   :"jsoncallback",
                  async   :false,
                  crossDomain :true,
                  success   : function(data,status){
                    //alert(1);
                    $.each(data, function(i,item){
                    	var appointmentcount = $('#appointmentcount').val();
                    	var propercount = $('#propercount').val();
                        appointmentcount = parseInt(appointmentcount) + 1;
				        propercount = parseInt(propercount) + 1;
				        $('.deleterow').show();
				       	var first = "<tr><td><input type='text' name='appointmentName"+propercount+"' placeholder='Enter Appointment Name..' maxlength='25' id='appointmentName"+propercount+"' class='forminputs2' value='"+item.AppointmentShortName+"'></td><td><input type='text' name='doctorName"+propercount+"' placeholder='Enter Doctor Name..' size='25px' maxlength='25' id='doctorName"+propercount+"' class='forminputs2' value='"+item.DoctorsName+"'></td><td><input type='text' name='appointment_date"+propercount+"' placeholder='Date' maxlength='25' id='appointment_date"+propercount+"' readonly class='forminputs2 appoinmentdate' value='"+item.AppointmentDate+"' style='height:30px'><input type='text' name='appointment_time"+propercount+"' placeholder='Time' maxlength='25' id='appointment_time"+propercount+"' readonly class='forminputs2 appointmenttime'value='"+item.AppointmentTime+"' style='height:30px;width:100%;'></td><td><select name='duration_inhours"+propercount+"' id='duration_inhours"+propercount+"' class='forminputs2 durationinhours' style='height:30px;width:100%;'>"+item.HourOptions+"</select><select name='duration_inmins"+propercount+"' id='duration_inmins"+propercount+"' class='forminputs2 durationinmins' style='height:30px;width:100%;'>"+item.MinuteOptions+"</select></td><td><textarea name='venue"+propercount+"' id='venue"+propercount+"' placeholder='Enter Venue for the appointment..' class='forminputs2'></textarea></td><td><textarea name='requirements"+propercount+"' id='requirements"+propercount+"' placeholder='Enter Requirements for the appointment..' class='forminputs2'>"+item.AppointmentRequirements+"</textarea></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"'></td></tr>";
				        var slno  = "<tr><td>"+appointmentcount+"</td></tr>";
				        $('#aslno > tbody').append(slno);
				        $('#adata > tbody').append(first);
				        var current_usedappointmentcount = $('#usedappointmentcount').val();
				        var new_usedappointmentcount = current_usedappointmentcount+propercount+",";
				        $('#usedappointmentcount').val(new_usedappointmentcount);
				        $('#appointmentcount').val(appointmentcount);
				        $('#propercount').val(propercount);
				        $('#appointmentName'+propercount).focus();
				    });
                  },
                  error: function(){

                  }
                });
		});
			$('.addassignedplanappointments').click(function(){
				var appo_id = $(this).attr('id');  
			   $("input").each(function() {
			    var id = $(this).attr("id");//id of the current textarea
			    var deleted_row_id  = id.replace("appointmentName", "");
			    if (id.indexOf("appointmentName") >= 0){

			    var appointment_name = $(this).val();
			   	if(appointment_name.replace(/\s+/g, '') == ""){
			      $('#aslno tr:last').remove();
			      //this.parentNode.parentNode.remove();
			      $(this).closest('tr').remove();
			     //alert(appointmentcount);
			     var appointmentcount = $('#appointmentcount').val();
			      appointmentcount = parseInt(appointmentcount) - 1;
			      //alert(appointmentcount);
			      if(appointmentcount == 1){
			        $('.deleterow').hide();
			      }
			        var deleted_usedappointment 		= deleted_row_id+",";
			        var current_usedappointmentcount 	= $('#usedappointmentcount').val();
			        var new_usedappointmentcount  		= current_usedappointmentcount.replace(deleted_usedappointment, "");
			        $('#usedappointmentcount').val(new_usedappointmentcount);
			        $('#appointmentcount').val(appointmentcount);
			    }
			    }
			});
			var master 		= appo_id.split("~~");
			var userid 		= master[0]
			var plancode 	= master[1];
			var appotime 	= master[2];
			var appodate 	= master[3];
			var dataString 	= "plancode="+plancode+"&type=get_assigned_appointments&time="+appotime+"&date="+appodate+"&userid="+userid;
				//bootbox.alert(dataString);
				$.ajax({
                  type    :"GET",
                  url     :"ajax_validation.php",
                  data    :dataString,
                  dataType  :"jsonp",
                  jsonp   :"jsoncallback",
                  async   :false,
                  crossDomain :true,
                  success   : function(data,status){
                    //alert(1);
                    $.each(data, function(i,item){
                    	var appointmentcount = $('#appointmentcount').val();
                    	var propercount = $('#propercount').val();
                        appointmentcount = parseInt(appointmentcount) + 1;
				        propercount = parseInt(propercount) + 1;
				        $('.deleterow').show();
				        var first = "<tr><td><input type='text' name='appointmentName"+propercount+"' placeholder='Enter Appointment Name..' maxlength='25' id='appointmentName"+propercount+"' class='forminputs2' value='"+item.AppointmentShortName+"'></td><td><input type='text' name='doctorName"+propercount+"' placeholder='Enter Doctor Name..' size='25px' maxlength='25' id='doctorName"+propercount+"' class='forminputs2' value='"+item.DoctorsName+"'></td><td><input type='text' name='appointment_date"+propercount+"' placeholder='Date' maxlength='25' id='appointment_date"+propercount+"' readonly class='forminputs2 appoinmentdate' value='"+item.AppointmentDate+"' style='height:30px'><input type='text' name='appointment_time"+propercount+"' placeholder='Time' maxlength='25' id='appointment_time"+propercount+"' readonly class='forminputs2 appointmenttime'value='"+item.AppointmentTime+"' style='height:30px;width:100%;'></td><td><select name='duration_inhours"+propercount+"' id='duration_inhours"+propercount+"' class='forminputs2 durationinhours' style='height:30px;width:100%;'>"+item.HourOptions+"</select><select name='duration_inmins"+propercount+"' id='duration_inmins"+propercount+"' class='forminputs2 durationinmins' style='height:30px;width:100%;'>"+item.MinuteOptions+"</select></td><td><textarea name='venue"+propercount+"' id='venue"+propercount+"' placeholder='Enter Venue for the appointment..' class='forminputs2'></textarea></td><td><textarea name='requirements"+propercount+"' id='requirements"+propercount+"' placeholder='Enter Requirements for the appointment..' class='forminputs2'>"+item.AppointmentRequirements+"</textarea></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"'></td></tr>";
				        var slno  = "<tr><td>"+appointmentcount+"</td></tr>";
				        $('#aslno > tbody').append(slno);
				        $('#adata > tbody').append(first);
				        var current_usedappointmentcount = $('#usedappointmentcount').val();
				        var new_usedappointmentcount = current_usedappointmentcount+propercount+",";
				        $('#usedappointmentcount').val(new_usedappointmentcount);
				        $('#appointmentcount').val(appointmentcount);
				        $('#propercount').val(propercount);
				        $('#appointmentName'+propercount).focus();
				                    });
                  },
                  error: function(){

                  }
                });
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
						url			: "procedureDefaultPage4.php",
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
			$('.panel-heading a').on('click',function(e){
				var id = $(this).attr('href');
				if($(id).hasClass('in')){
					//alert(1);
					//$(id).removeClass('in');
					//$('.panel-collapse').removeClass('in');		
				} else {
					//alert(2);
					$(id).addClass('in');
				    $('.panel-collapse').removeClass('in');		
				}

			});
			//FINISHED ADDING
			$('#finished_adding').click(function(){
				var plan_name = $('#plan_name').val();
			    	if(plan_name.replace(/\s+/g, '') == ""){
						bootbox.alert("Please enter a title for this plan.");
						$('.bootbox').on('hidden.bs.modal', function() {
						    $('#plan_name').focus();
						});
						$('#plandetailsmodal').modal('show');
						$('#plan_name').val("");
						return false;
					}
				window.location.href = "finishedadding_new.php";
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