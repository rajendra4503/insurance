<?php 
session_start();
//ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php'); 
//$_SESSION['current_created_plancode'] = "RJKR10011003";
//$_SESSION['current_created_plancode'] = "ASPL10011001";
//$_SESSION['current_created_planname'] = "Diabetic Diet Plan";
//$_SESSION['current_created_planname'] = "Test Plan";
$current_created_plancode = $_SESSION['current_created_plancode'];
$current_created_planname = $_SESSION['current_created_planname'];
if((isset($_REQUEST['hidden_value']))&&(!empty($_REQUEST['hidden_value']))){
	//echo "<pre>";print_r($_REQUEST);exit;
	$selftestcount    			= mysql_real_escape_string(trim($_REQUEST['selftestcount'])); //Total number of self tests present on screen
	$usedselftestcount 			= mysql_real_escape_string(trim($_REQUEST['usedselftestcount'])); //row ids of medicines present
	$selfttestids 				= explode(",", $usedselftestcount);
	$get_last_selftest_num 		= mysql_query("select max(SelfTestID) from SELF_TEST_HEADER where PlanCode = '$current_created_plancode'");
	$selftest_count 			= mysql_num_rows($get_last_selftest_num);
	if($selftest_count > 0){
		while($last_selftest_num 	= mysql_fetch_array($get_last_selftest_num)){
			$last_selftest 		= (empty($last_selftest_num['max(SelfTestID)'])) 		? 0 : $last_selftest_num['max(SelfTestID)'];
		} 		
	} else {
			$last_selftest      = 0;
	}
	//echo $last_selftest;exit;
	//print_r($medicineids);exit;
	$current_selftest_num 		= $last_selftest + 1;
	$insert_header_details 		= "insert into SELF_TEST_HEADER (Plancode, SelfTestID, CreatedDate, CreatedBy) values ('$current_created_plancode', '$current_selftest_num', now(), '$logged_userid')";
	//echo $insert_header_details;exit;
	$insert_header_run  	= mysql_query($insert_header_details);
    $count = 1;
	foreach ($selfttestids as $ids) {
		if($ids != ""){
			$selftestName = mysql_real_escape_string(trim($_REQUEST["selftestName$ids"]));
			if($selftestName != ""){
			$doctor_name 				= mysql_real_escape_string(trim($_REQUEST["doctorName$ids"]));
			$when 		  = mysql_real_escape_string(trim($_REQUEST["when$ids"]));
			$frequency 	  = mysql_real_escape_string(trim($_REQUEST["frequency$ids"]));
			$linkentered  = mysql_real_escape_string(trim($_REQUEST["linkentered$ids"]));
			$selftestdesc = mysql_real_escape_string(trim($_REQUEST["selftestdesc$ids"]));
			if($frequency == "Weekly"){
				$frequencystring 	  = mysql_real_escape_string(trim($_REQUEST["selectedweekdays$ids"]));
			} else if($frequency == "Monthly"){
				$frequencystring 	  = mysql_real_escape_string(trim($_REQUEST["selectedmonthdays$ids"]));
			} else {
				$frequencystring = NULL;
			}
			$frequencystring 	  = rtrim($frequencystring,",");
			if($frequency == "Once"){
				$howlong 		= NULL;
				$howlongtype 	= NULL;
			} else {
				$howlong 		= mysql_real_escape_string(trim($_REQUEST["count$ids"]));
				$howlongtype 	= mysql_real_escape_string(trim($_REQUEST["countType$ids"]));
			}
			$responserequired = "N";
			if(isset($_REQUEST["response$ids"])){
				$responserequired = mysql_real_escape_string(trim($_REQUEST["response$ids"]));
			}
			if($responserequired != "Y"){
				$responserequired = "N";
			}
			$startflag 		= mysql_real_escape_string(trim($_REQUEST["radio$ids"]));
			if($startflag == "PS"){
				$numberofdaysafterplan 	= NULL;
				$specific_date 			= NULL;
			}else if($startflag == "ND"){
				$numberofdaysafterplan 	= mysql_real_escape_string(trim($_REQUEST["numofdays$ids"]));
				$specific_date 			= NULL;
			} else if($startflag == "SD"){
				$numberofdaysafterplan 	= NULL;
				$specific_date 			= mysql_real_escape_string(trim($_REQUEST["specificdate$ids"]));
				$specific_date			= date('Y-m-d',strtotime($specific_date));
			}
			$insert_selfttest_details = "insert into SELF_TEST_DETAILS (`PlanCode`,`SelfTestID`,`RowNo`,`TestName`,`DoctorsName`,`TestDescription`, `Instruction`, `Frequency`,`FrequencyString`,`HowLong`,`HowLongType`,`Link`,`ResponseRequired`,`StartFlag`,`NoOfDaysAfterPlanStarts`,`SpecificDate`,`CreatedDate`,`CreatedBy`) values ('$current_created_plancode', '$current_selftest_num', '$count', '$selftestName','$doctor_name','$selftestdesc','$when','$frequency','$frequencystring','$howlong','$howlongtype','$linkentered','$responserequired','$startflag','$numberofdaysafterplan','$specific_date', now(), '$logged_userid');";
			//echo $insert_selfttest_details;exit;
			$insert_header_run  	= mysql_query($insert_selfttest_details);
			$check_affected_rows= mysql_affected_rows();
		        if($check_affected_rows)
		       { 
		       	//header("Location:plan_selftest.php");
		       } else {
		       	//header("Location:plan_selftest.php");
		       }
		}
	}
		$count++;
	}
	//$update_plan_header = mysql_query("update PLAN_HEADER set PlanStatus = 'A' where Plancode='$current_created_plancode'");
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Plan Piper | Self Test</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/ndatepicker.css">
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
			<div id="plantitle"><?php echo $current_created_planname;?>
			<button type="button" class="btns" align="right" id="finished_adding"><img src="images/finishAdd.png" style="height:20px;width:auto;margin-bottom:3px;">&nbsp;FINISH ADDING</button>
			</div>
		    </div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<ul class="nav nav-pills nav-justified">
					<?php
					echo $modules;
					?>
					<!--<li role="presentation" class="navbartoptabs"><a href="plan_medication.php">MEDICATION</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_appointments.php">APPOINTMENT</a></li>
					<li role="presentation" class="active navbartoptabs"><a href="plan_selftest.php">SELF TEST</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_labtest.php">LAB TEST</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_diet.php">DIET</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_exercise.php">EXERCISE</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_instruction.php">INSTRUCTION</a></li>-->
				</ul>
			</div>
			    <div  style="height:100%;">
    	<div class="col-lg-2 col-md-3 col-sm-3 hidden-xs paddingrl0" style="margin-right:0px;padding-right:0px;" id="activitylist">
    	<div class="navbar navbar-default" role="navigation" style="height: 100%;background-color:#004F35;position:fixed;width:16.7%;">
    		<div id="listBar">
		    	<div id="listHeader">
		    		<h6 style="font-family:Freestyle;font-size:180%;margin-top:-7px;letter-spacing:1px;">Test Schedule</h6>
		    	</div>
		    	<div id="listItems">
		    		<button type="button" id="addItemButton" class="btns">Add Tests<img src="images/addItem.png"></button>
		    		<?php 
		    		$get_plan_selftests 		= "select PlanCode, SelfTestID, CreatedDate from SELF_TEST_HEADER where PlanCode = '$current_created_plancode'";
		    		$get_plan_selftests_run 	= mysql_query($get_plan_selftests);
		    		$get_plan_selftests_count 	= mysql_num_rows($get_plan_selftests_run);
		    		if($get_plan_selftests_count > 0){
			    		while ($get_selftest_row = mysql_fetch_array($get_plan_selftests_run)) {
			    			$selftest_no   = $get_selftest_row['SelfTestID'];
			    			$selftest_date = date('d-M-Y',strtotime($get_selftest_row['CreatedDate'])); 
			    			?>
			    			<button type="button" class="btns editselftestbuttons" id="<?php echo $selftest_no; ?>">
				    			<span>Self Test - <?php echo $selftest_no;?></span><br>
				    			<span>Created on <?php echo $selftest_date;?></span>
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
	    			You must first add self test details. <span id='getselftests'>Click here</span> to start adding or Select A Template to get started.
	    		</label>
	    	</div>
    	</div>
    </div>
		</div>
		<!--SHOW WEEK DAY PICKER MODAL WINDOW-->
            <div class="modal" id="weekdaypicker" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header padding0" align="center" style="border:none;">
                   <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h5 class="modal-title" id="modalheadings">Select Weekdays</h5>
                  </div>
                  <div class="modal-body weekdayoptions" align="center" style="padding-top:0px;background-color:#fff;padding-bottom:50px;">
                	
               	  </div>
               	  <div class="margin10" align="center">
               	  <input type="hidden" name="weeklyselectedid" id="weeklyselectedid" value="0">
               	  <button class="smallbutton" id="weeklydaysselected">Done</button>
               	  </div>
              	</div>
              </div>
            </div>
    <!--END OF SHOW WEEK DAY PICKER MODAL WINDOW-->
     <!--SHOW MONTH DAY PICKER MODAL WINDOW-->
            <div class="modal" id="monthdaypicker" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header padding0" align="center" style="border:none;">
                   <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h5 class="modal-title" id="modalheadings">Select Days</h5>
                  </div>
                  <div class="modal-body monthdayoptions" align="center" style="padding-top:0px;background-color:#fff;padding-bottom:50px;">
                	
               	  </div>
               	  <div class="margin10" align="center">
               	  <input type="hidden" name="monthlyselectedid" id="monthlyselectedid" value="0">
               	  <button class="smallbutton" id="monthlydaysselected">Done</button>
               	  </div>
              	</div>
              </div>
            </div>
    <!--END OF SHOW MONTH DAY PICKER MODAL WINDOW-->
		<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/ndatepicker-ui.js"></script>
		<script type="text/javascript" src="js/bootbox.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			$('#5').addClass('active');
			mainHeader = 115;//include main header height also
			plantitleHeight = $("#plantitle").outerHeight(true);//true includes margin height also
			navigationbarHeight = $("#navigationbar").outerHeight(true);//true includes margin height also
			totalUsedHeight = plantitleHeight + navigationbarHeight + mainHeader;
			listBarHeight = ($(window).innerHeight()-totalUsedHeight)
		  	//$('#listBar').css({height: listBarHeight});
		  	$('#dynamicPagePlusActionBar').css({height: listBarHeight});
			var selftestcount = 0;
			$('#plapiper_pagename').html("Self Tests");
			
			var h = window.innerHeight;
		  	var windowheight = h;
       		var available_height = h - 210;
        	$('.maincontent').height(available_height);

			$(document).on('focus', '.specificdate', function () {
				$(this).datepicker({
			        dateFormat: "dd-M-yy",
			        minDate: 0,
			        changeMonth: true,
			        changeYear: true,
			     });
			});
			setTimeout(function() {
		        $("#addItemButton").trigger('click');
		    },1);
			$('#addItemButton, #getselftests').click(function(){
				$.ajax({
					type        : "GET",
					url			: "selfTestDefaultPage.php",
					dataType	: "html",
					success	: function (response)
					{ 
						$('#dynamicPagePlusActionBar').html(response);
						selftestcount = 3;
					 },
					 error: function(error)
					 {
					 	bootbox.alert(error);
					 }
				}); 		
			});
			//ON CHANGE OF FREQUENCY RESTRICT DURATION INPUTS
			$(document).on('change', '.testfrequency', function () {
			   var freqid = $(this).attr('id');
			   var id  = freqid.replace("frequency", "");
			   //bootbox.alert(id);
			   var value = this.value;
			   //bootbox.alert(value);
			   if(value == "Once"){
			   	$('#count'+id).prop('disabled', true);
			   	$('#count'+id).css('opacity', '0.2');
				$('#countType'+id).prop('disabled', true);
				$('#countType'+id).css('opacity', '0.2');
				$('#selectedmonthdays'+id).attr('type', 'hidden');
				$('#selectedweekdays'+id).attr('type', 'hidden');
			   }else{
			   	$('#count'+id).prop('disabled', false);
			   	$('#count'+id).css('opacity', '1');
				$('#countType'+id).css('opacity', '1');
				$('#countType'+id).prop('disabled', false);
				if(value == "Daily"){
					$('#countType'+id).empty().append("<option value='0' style='display:none;'>select</option><option value='Days' selected>Days</option><option value='Weeks'>Weeks</option><option value='Months'>Months</option>");
					$('#selectedmonthdays'+id).attr('type', 'hidden');
					$('#selectedweekdays'+id).attr('type', 'hidden');
				}
				 else if(value == "Weekly"){
					var weekdayoptions = "<div class='btn-group' data-toggle='buttons'><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Sun' class='weekdaycheck'> Sun</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Mon' class='weekdaycheck'> Mon</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Tue' class='weekdaycheck'> Tue</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Wed' class='weekdaycheck'> Wed</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Thu' class='weekdaycheck'> Thu</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Fri' class='weekdaycheck'> Fri</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Sat' class='weekdaycheck'> Sat</label></div>";
					$('#weeklyselectedid').val(id);
					$('.weekdayoptions').html(weekdayoptions);
					$('#monthdaypicker').modal('hide');
					$('#weekdaypicker').modal('show');
					$('#countType'+id).empty().append("<option value='0' style='display:none;'>select</option><option value='Weeks' selected>Weeks</option><option value='Months'>Months</option>");
					$('#selectedweekdays'+id).attr('type', 'hidden');
				}
				else if(value == "Monthly"){
					$('#countType'+id).empty().append("<option value='0' style='display:none;'>select</option><option value='Months' selected>Months</option>");
					var monthdayoptions = "<div class='btn-group' data-toggle='buttons' align='center'>";
					for(i = 1; i <= 28; i++){
						i=(i<10) ? '0'+i : i;
						monthdayoptions = monthdayoptions + "<label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='monthdaycheck' class='monthdaycheck' value='"+i+"'> "+i+"</label>";
					}
					monthdayoptions = monthdayoptions + "<label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 29</label><label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 30</label><label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 31</label></div>";
				
					$('#monthlyselectedid').val(id);
					$('.monthdayoptions').html(monthdayoptions);
					$('#weekdaypicker').modal('hide');
					$('#monthdaypicker').modal('show');
					$('#selectedmonthdays'+id).attr('type', 'hidden');
			   }
			}
			});
			//ON CLICK OF DONE BUTTON AFTER SELECTING THE WEEKDAYS
			$(document).on('click', '#weeklydaysselected', function () {
				var getcurrentid = $('#weeklyselectedid').val();
				var chkId = "";
				$('.weekdaycheck:checked').each(function() {
				  chkId += $(this).val() + ",";
				});
				//bootbox.alert(chkId);
				if(chkId == ""){
					bootbox.alert("Please select atleast one week day to continue..");
					return false;
				}
				$('#selectedweekdays'+getcurrentid).val(chkId);
				$('#weekdaypicker').modal('hide');
				$('#selectedmonthdays'+getcurrentid).attr('type', 'hidden');
				$('#selectedweekdays'+getcurrentid).attr('type', 'text');
			});
			//ON CLICK OF DONE BUTTON AFTER SELECTING THE MONTH DAYS
			$(document).on('click', '#monthlydaysselected', function () {
				var getcurrentid = $('#monthlyselectedid').val();
				var chkId2 = "";
				$('.monthdaycheck:checked').each(function() {
				  chkId2 += $(this).val() + ",";
				});
				//bootbox.alert(chkId);
				if(chkId2 == ""){
					bootbox.alert("Please select atleast one day to continue..");
					return false;
				}
				$('#selectedmonthdays'+getcurrentid).val(chkId2);
				$('#monthdaypicker').modal('hide');
				$('#selectedweekdays'+getcurrentid).attr('type', 'hidden');
				$('#selectedmonthdays'+getcurrentid).attr('type', 'text');
			});
			//ON CLICK SHOW WEEK DAY MODAL WINDOW TO EDIT SELECTION
			$(document).on('click','.editselectedweekday', function(){
			   var editweekdayid = $(this).attr('id');
			   var id  = editweekdayid.replace("selectedweekdays", "");
			   //bootbox.alert(id);
			   var selectedweekdays = $('#selectedweekdays'+id).val();
			  // bootbox.alert(selectedweekdays);
			  var weekdayresult = selectedweekdays.split(',');
				var selectedweekdaygroup = "<div class='btn-group' data-toggle='buttons'><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Sun' class='weekdaycheck'> Sun</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Mon' class='weekdaycheck' > Mon</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Tue' class='weekdaycheck'> Tue</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Wed' class='weekdaycheck'> Wed</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Thu' class='weekdaycheck'> Thu</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Fri' class='weekdaycheck'> Fri</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Sat' class='weekdaycheck'> Sat</label></div>";
					$('#weeklyselectedid').val(id);
					$('.weekdayoptions').html(selectedweekdaygroup);
					$('.weekdaycheck').each(function() {
                    if(jQuery.inArray($(this).val(),weekdayresult) != -1){
                    	$(this).prop('checked',true);
                    	$(this).parents('label').addClass('active'); 
                    }
                	});
					$('#monthdaypicker').modal('hide');
					$('#weekdaypicker').modal('show');
			});
			//ON CLICK SHOW MONTH DAY MODAL WINDOW TO EDIT SELECTION
			$(document).on('click','.editselectedmonthday', function(){
				var editmonthdayid = $(this).attr('id');
				var id  = editmonthdayid.replace("selectedmonthdays", "");
			   //bootbox.alert(id);
			   var selectedmonthdays = $('#selectedmonthdays'+id).val();
			  // bootbox.alert(selectedmonthdays);
			  var monthdayresult = selectedmonthdays.split(',');
			  var monthdayoptions = "<div class='btn-group' data-toggle='buttons' align='center'>";
					for(i = 1; i <= 28; i++){
						i=(i<10) ? '0'+i : i;
				monthdayoptions = monthdayoptions + "<label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='monthdaycheck' class='monthdaycheck' value='"+i+"'> "+i+"</label>";
					}
				monthdayoptions = monthdayoptions + "<label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 29</label><label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 30</label><label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 31</label></div>";				
					$('#monthlyselectedid').val(id);
					$('.monthdayoptions').html(monthdayoptions);
					$('.monthdaycheck').each(function() {
                    if(jQuery.inArray($(this).val(),monthdayresult) != -1){
                    	$(this).prop('checked',true);
                    	$(this).parents('label').addClass('active'); 
                    }
                	});
					$('#weekdaypicker').modal('hide');
					$('#monthdaypicker').modal('show');
			});
			//ON CHANGE OF 'START' RADIO, DISABLE AND ENABLE INPUT FIELDS 
			$(document).on('change', '.prescriptionradio', function () {
					var radioname = $(this).attr('name');
					//bootbox.alert(radioname);
					var id  = radioname.replace("radio", "");
					var radiovalue = $(this).val();
					//bootbox.alert(radiovalue);
					if(radiovalue == "PS"){
						$('#numofdays'+id).addClass("pointernone");
						$('#specificdate'+id).addClass("pointernone");
					} else if(radiovalue == "ND"){
						$('#numofdays'+id).removeClass("pointernone");
						$('#specificdate'+id).addClass("pointernone");
					} else if(radiovalue == "SD"){
						$('#numofdays'+id).addClass("pointernone");
						$('#specificdate'+id).removeClass("pointernone");
					}
				});
							//ON CLICK OF SAVE BUTTON
				$(document).on('click', '#saveAndEdit', function () {
				var numberOfSelfTests = 0;
				var current_usedselftestcount = $('#usedselftestcount').val();//ID OF ALL THE FORM ROW ELEMENTS PRESENT SEPERATED BY COMMAS. EG: 1,2,3,
				var result = current_usedselftestcount.split(',');
				selftestcount = $('#selftestcount').val(); //TOTAL NUMBER OF MEDICINE FIELDS PRESENT CURRENTLY ON THE PAGE
				for (i = 0; i < selftestcount; ++i) {
				 	var selftestname = $('#selftestName'+result[i]).val();

				 	if(!selftestname == ""){
				 		if(selftestname.replace(/\s+/g, '') == ""){
					 		bootbox.alert("Self Test name cannot be left blank");
					 		$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#selftestName'+result[i]).focus();
							});
					 		$('#selftestName'+result[i]).val("");
							return false;
				 		}
				 		numberOfSelfTests = numberOfSelfTests + 1;
				 		var current_doctor_name = $('#doctorName'+result[i]).val();
						if(current_doctor_name.replace(/\s+/g, '') == ""){
							bootbox.alert("Please enter the doctors name");
							$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#doctorName'+result[i]).focus();
							});
							$('#doctorName'+result[i]).val("");
							return false;
						}
				 		var wheninput = $('#when'+result[i]).val();
				 		//bootbox.alert(wheninput);
				 		if(wheninput == 0){
				 			bootbox.alert("Please select the self test time");
				 			$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#when'+result[i]).focus();
							});
				 			return false;
				 		}
				 		var frequencyinput = $('#frequency'+result[i]).val();
				 		//bootbox.alert(wheninput);
				 		if(frequencyinput == 0){
				 			bootbox.alert("Please select the  test frequency");
				 			$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#frequency'+result[i]).focus();
				 			return false;
				 		});
				 	}
				 		if(frequencyinput!= "Once"){
					 		var countinput = $('#count'+result[i]).val();
					 		//bootbox.alert(wheninput);
					 		if((countinput == "")||(countinput == 0)||(countinput == "0")){
					 			bootbox.alert("Please enter the test duration");
					 			$('.bootbox').on('hidden.bs.modal', function() { 
								    $('#count'+result[i]).focus();
								});
					 			return false;
					 		}
					 		if(!$.isNumeric(countinput)){
				 				bootbox.alert("Please enter a numeric value");
				 				$('.bootbox').on('hidden.bs.modal', function() { 
								    $('#count'+result[i]).focus();
								});
					 			return false;
				 			}
					 		var countTypeinput = $('#countType'+result[i]).val();
					 		//bootbox.alert(wheninput);
					 		if(countTypeinput == 0){
					 			bootbox.alert("Please select the test duration");
					 			$('.bootbox').on('hidden.bs.modal', function() { 
								    $('#countType'+result[i]).focus();
								});
					 			return false;
					 		}				 			
				 		}
				 		if(frequencyinput == "Weekly"){
				 			var selectedweekdays = $('#selectedweekdays'+result[i]).val();
				 			if(selectedweekdays.replace(/\s+/g, '') == ""){
				 				bootbox.alert("Please select atleast one week day to continue..");
				 				var weekdayoptions = "<div class='btn-group' data-toggle='buttons'><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Sun' class='weekdaycheck'> Sun</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Mon' class='weekdaycheck'> Mon</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Tue' class='weekdaycheck'> Tue</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Wed' class='weekdaycheck'> Wed</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Thu' class='weekdaycheck'> Thu</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Fri' class='weekdaycheck'> Fri</label><label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='weekdaycheck' value='Sat' class='weekdaycheck'> Sat</label></div>";
								$('#weeklyselectedid').val(result[i]);
								$('.weekdayoptions').html(weekdayoptions);
								$('#monthdaypicker').modal('hide');
								$('#weekdaypicker').modal('show');
								return false;
				 			}
				 		}
				 		if(frequencyinput == "Monthly"){
				 			var selectedmonthdays = $('#selectedmonthdays'+result[i]).val();
				 			if(selectedmonthdays.replace(/\s+/g, '') == ""){
				 				bootbox.alert("Please select atleast one day to continue..");
								var monthdayoptions = "<div class='btn-group' data-toggle='buttons' align='center'>";
								for(j = 1; j <= 28; j++){
									monthdayoptions = monthdayoptions + "<label class='btn btn-primary'><input type='checkbox' autocomplete='off' name='monthdaycheck' class='monthdaycheck' value='"+j+"'> "+j+"</label>";
								}
								monthdayoptions = monthdayoptions + "<label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 29</label><label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 30</label><label class='btn btn-primary disabled'><input type='checkbox' autocomplete='off' disabled> 31</label></div>";
								$('#monthlyselectedid').val(result[i]);
								$('.monthdayoptions').html(monthdayoptions);
								$('#weekdaypicker').modal('hide');
								$('#monthdaypicker').modal('show');
				 				return false;
				 			}
				 		}
				 		var selected = $(".radio"+result[i]+":checked").val();
				 		//bootbox.alert(selected);
				 		if(selected == "PS"){

				 		} else if(selected == "ND"){
				 			var numofdaysentered = $('#numofdays'+result[i]).val();
				 			if((numofdaysentered == "")||(numofdaysentered == 0)||(numofdaysentered == "0")){
				 				bootbox.alert("Please enter the number of days");
				 				$('.bootbox').on('hidden.bs.modal', function() { 
								    $('#numofdays'+result[i]).focus();
								});
					 			return false;
				 			}
				 			if(!$.isNumeric(numofdaysentered)){
				 				bootbox.alert("Please enter a numeric value");
				 				$('.bootbox').on('hidden.bs.modal', function() { 
								    $('#numofdays'+result[i]).focus();
								});
					 			return false;
				 			}

				 		} else if(selected == "SD"){
				 			var specificdateentered = $('#specificdate'+result[i]).val();
				 			if(specificdateentered == ""){
				 				bootbox.alert("Please select the specific date");
				 				$('.bootbox').on('hidden.bs.modal', function() { 
								    $('#specificdate'+result[i]).focus();
								});
					 			return false;
				 			}
				 		}
				 		var linkentered = $('#linkentered'+result[i]).val();
						if(linkentered.replace(/\s+/g, '') == ""){

				 		} else {
				 			if(/^([a-z]([a-z]|\d|\+|-|\.)*):(\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?((\[(|(v[\da-f]{1,}\.(([a-z]|\d|-|\.|_|~)|[!\$&'\(\)\*\+,;=]|:)+))\])|((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)(:\d*)?)(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*|(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)){0})(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(linkentered)) {
							} else {
							  bootbox.alert("Please enter a valid url");
							  $('.bootbox').on('hidden.bs.modal', function() { 
								    $('#linkentered'+result[i]).focus();
								});
					 			return false;
							}
				 		}
				 	}
				}
				if(numberOfSelfTests == 0){
					bootbox.alert("Please enter atleast one self test to continue.");
					$('.bootbox').on('hidden.bs.modal', function() { 
						$('#selftestName1').focus();	    
					});
					return false;
				}
				$('#frm_plan_selftest').submit();
			});
			//FINISHED ADDING
			$('#finished_adding').click(function(){
				window.location.href = "finishedadding.php";
			});
			//EDIT SELFTEST BUTTON CLICKED - FROM SIDE PANEL
			$('.editselftestbuttons').click(function(){
			var appoid = $(this).attr('id');
				//bootbox.alert(appoid);
			window.location.href = "edit_plan_selftest.php?id="+appoid;
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