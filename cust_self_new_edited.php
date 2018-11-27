<?php
session_start();
//ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php');
//$_SESSION['plancode_for_current_plan'] 	= "IN0000000022";
$plancode_for_current_plan 				= $_SESSION['plancode_for_current_plan'];
$userid_for_current_plan 				= $_SESSION['userid_for_current_plan'];
$planname_for_current_plan_text 		= "Click to edit Plan Details";
$planname_for_current_plan = "";
$plandesc_for_current_plan = "";
if(isset($_SESSION['plancode_for_current_plan'])){
	$plancode_for_current_plan = $_SESSION['plancode_for_current_plan'];
	//Get Plan Details
		if($plancode_for_current_plan != ""){
		$get_plan_details = "select PlanCode, PlanName, PlanDescription from USER_PLAN_HEADER where PlanCode = '$plancode_for_current_plan' and UserID='$userid_for_current_plan'";
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
	//DELETE Self TESTS

	$selftestcount    			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['selftestcount']))); //Total number of Self tests present on screen
	$usedselftestcount 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['usedselftestcount']))); //row ids of medicines present
	$selfttestids 				= explode(",", $usedselftestcount);
	$current_selftest_num 		= "1";
	$self_edit_id = "1";
	if($selftestcount > '0'){
		$delete_user_header = mysql_query("delete from USER_SELF_TEST_HEADER where  PlanCode = '$plancode_for_current_plan' and SelfTestID='$self_edit_id' and UserID='$userid_for_current_plan'");
		$delete_user_details = mysql_query("delete from USER_SELF_TEST_DETAILS where  PlanCode = '$plancode_for_current_plan' and SelfTestID='$self_edit_id' and UserID='$userid_for_current_plan'");	
	}
	$insert_header_details 		= "insert into USER_SELF_TEST_HEADER (UserID, Plancode, SelfTestID, CreatedDate, CreatedBy) values ('$userid_for_current_plan','$plancode_for_current_plan', '$current_selftest_num', now(), '$logged_userid')";
	//echo $insert_header_details;exit;
	$insert_header_run  	= mysql_query($insert_header_details);
    $count = 1;
	foreach ($selfttestids as $ids) {
		if($ids != ""){
			$selftestName = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["selftestName$ids"])));
			$selftestOther = "";
			if($selftestName == "0"){ //If they select other option in medical test dropdown
				$selftestOther = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["selftestOther$ids"])));
			} else {
			  $get_medical_test1 = mysql_query("select ID, TestName from MEDICAL_TESTS where ID = '$selftestName'");
			  $get_medical_test_count1 = mysql_num_rows($get_medical_test1);
			  if($get_medical_test_count1 > 0){
			    while ($medical_test1 = mysql_fetch_array($get_medical_test1)) {
			      $selftestOther = $medical_test1['TestName'];
			    }
			  }
			}
			if($selftestName != "select"){
			$doctor_name  = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["doctorName$ids"])));
			$when 		  = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["when$ids"])));
			$frequency 	  = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["frequency$ids"])));
			$linkentered  = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["linkentered$ids"])));
			$selftestdesc = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["selftestdesc$ids"])));
			if($frequency == "Weekly"){
				$frequencystring 	  = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["selectedweekdays$ids"])));
			} else if($frequency == "Monthly"){
				$frequencystring 	  = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["selectedmonthdays$ids"])));
			} else {
				$frequencystring = NULL;
			}
			$frequencystring 	  = rtrim($frequencystring,",");
			if($frequency == "Once"){
				$howlong 		= NULL;
				$howlongtype 	= NULL;
			} else {
				$howlong 		= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["count$ids"])));
				$howlongtype 	= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["countType$ids"])));
			}
			$responserequired = "Y";
			$startflag 		= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["radio$ids"])));
			if($startflag == "PS"){
				$numberofdaysafterplan 	= NULL;
				$specific_date 			= NULL;
			}else if($startflag == "ND"){
				$numberofdaysafterplan 	= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["numofdays$ids"])));
				$specific_date 			= NULL;
			} else if($startflag == "SD"){
				$numberofdaysafterplan 	= NULL;
				$specific_date 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["specificdate$ids"])));
				$specific_date			= date('Y-m-d',strtotime($specific_date));
			}
			$insert_selfttest_details = "insert into USER_SELF_TEST_DETAILS (`UserID`,`PlanCode`,`SelfTestID`,`RowNo`,`MedicalTestID`,`TestName`,`DoctorsName`,`TestDescription`, `InstructionID`, `Frequency`,`FrequencyString`,`HowLong`,`HowLongType`,`Link`,`ResponseRequired`,`StartFlag`,`NoOfDaysAfterPlanStarts`,`SpecificDate`,`CreatedDate`,`CreatedBy`) values ('$userid_for_current_plan','$plancode_for_current_plan', '$current_selftest_num', '$count','$selftestName', '$selftestOther','$doctor_name','$selftestdesc','$when','$frequency','$frequencystring','$howlong','$howlongtype','$linkentered','$responserequired','$startflag','$numberofdaysafterplan','$specific_date', now(), '$logged_userid');";
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
	//$update_plan_header = mysql_query("update PLAN_HEADER set PlanStatus = 'A' where Plancode='$plancode_for_current_plan'");

	  	$update_header = mysql_query("update USER_PLAN_HEADER set PlanUpdatedDate = current_timestamp where PlanCode='$plancode_for_current_plan' and UserID = '$userid_for_current_plan'");
  		header("Location:cust_self_new_edited.php");
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
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 paddingrl0" align="right"  id="plantitle"><button type="button" class="btns" align="right" id="finished_adding"><img src="images/finishAdd.png" style="height:20px;width:auto;margin-bottom:3px;">&nbsp;FINISH CUSTOMIZING</button></div>
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
    		<h6 style="font-family:Freestyle;font-size:33px;margin-top:-1px;letter-spacing:1px;color:#f2bd43;background-color:#000;">Self Test List</h6>
    		<div class="sidebarheadings">Master Self Tests :</div>
    			<div class="panel-group masterplanactivities" id="accordion1" role="tablist" aria-multiselectable="true" style="max-height:250px;overflow:scroll;overflow-x:hidden;">
			    		<?php
			    		$get_plan_selftests 		= "select t1.PlanCode, t1.SelfTestID, t1.RowNo, t1.TestName,
			    		 t1.DoctorsName from SELF_TEST_DETAILS as t1,PLAN_HEADER as t2 
			    		where t1.PlanCode=t2.PlanCode and t2.MerchantID='$logged_merchantid' and t2.PlanStatus='A'";
		    		$get_plan_selftests_run 	= mysql_query($get_plan_selftests);
		    		$get_plan_selftests_count 	= mysql_num_rows($get_plan_selftests_run);
		    		if($get_plan_selftests_count > 0){
			    		while ($get_selftest_row 	= mysql_fetch_array($get_plan_selftests_run)) {
			    			$selftest_id 	= $get_selftest_row['SelfTestID'];
			    			$selftest_row 	= $get_selftest_row['RowNo'];
			    			$selftest_name 	= $get_selftest_row['TestName'];
			    			$selftest_doc  	= $get_selftest_row['DoctorsName'];
			    			$selftest_code 	= $get_selftest_row['PlanCode'];
			    			?>
			    			 <div class="panel panel-default plancreationpanel">
					              <div class="panel-heading plancreationaccordion" role="tab" id="heading<?php echo $selftest_code.$selftest_id.$selftest_row; ?>">
					                <h4 class="panel-title" style="text-align:center;">
					                  <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $selftest_code.$selftest_id.$selftest_row; ?>" aria-expanded="false" aria-controls="collapse"><img src="images/rightarrow.png" style="height:12px;width:auto;" align="left" id="arrow<?php echo $selftest_code.$selftest_id.$selftest_row; ?>" onclick='changeimage("<?php echo $selftest_code.$selftest_id.$selftest_row; ?>");'></a>
					                   <?php echo $selftest_name;?>
					                  <img src="images/addtoright.png" class="addmasterplanselftests" align="right" style="height:20px;width:auto;cursor:pointer;background-color:#f2bd43;" id="<?php echo $selftest_code."~~".$selftest_id."~~".$selftest_row; ?>" title="Add to current plan">
					                </h4>
					              </div>
					              <div id="collapse<?php echo $selftest_code.$selftest_id.$selftest_row; ?>" class="panel-collapse collapse plancreationpanelbody" role="tabpanel" aria-labelledby="heading<?php echo $selftest_code.$selftest_id.$selftest_row; ?>">
					                <div class="panel-body"  style="text-align:center;">
					                	<?php
					                		echo $selftest_doc;
					                	?>
					                </div>
					              </div>
					            </div>
			    			<?php
			    		}
		    		} else {
		    			echo "<div style='color:#fff;'>No Self Tests to show</div>";
		    		}
			    		?>
		    		</div>
		    		
		    		<div class="sidebarheadings" style="margin-top: -13px;">Assigned Self Tests :</div>
		    				    		<div class="panel-group assignedplanactivities" id="accordion2" role="tablist" aria-multiselectable="true" style="overflow:scroll;overflow-x:hidden;">
			    		<?php
			    		$get_plan_selftests 		= "select  t1.UserID, t1.PlanCode, t1.SelfTestID, t1.RowNo,
			    		t1.TestName, t1.DoctorsName from USER_SELF_TEST_DETAILS as t1,USER_PLAN_HEADER as t2 
			    		where t1.PlanCode=t2.PlanCode and t1.UserID=t2.UserID and t2.MerchantID='$logged_merchantid'";
		    		$get_plan_selftests_run 	= mysql_query($get_plan_selftests);
		    		$get_plan_selftests_count 	= mysql_num_rows($get_plan_selftests_run);
		    		if($get_plan_selftests_count > 0){
			    		while ($get_selftest_row 	= mysql_fetch_array($get_plan_selftests_run)) {
			    			$selftest_user  = $get_selftest_row['UserID'];
			    			$selftest_id 	= $get_selftest_row['SelfTestID'];
			    			$selftest_row 	= $get_selftest_row['RowNo'];
			    			$selftest_name 	= $get_selftest_row['TestName'];
			    			$selftest_doc  	= $get_selftest_row['DoctorsName'];
			    			$selftest_code 	= $get_selftest_row['PlanCode'];
			    			?>
			    			 <div class="panel panel-default plancreationpanel">
					              <div class="panel-heading plancreationaccordion" role="tab" id="heading<?php echo $selftest_code.$selftest_id.$selftest_user.$selftest_row; ?>">
					                <h4 class="panel-title" style="text-align:center;">
					                  <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $selftest_code.$selftest_id.$selftest_user.$selftest_row; ?>" aria-expanded="false" aria-controls="collapse"><img src="images/rightarrow.png" style="height:12px;width:auto;" align="left" id="arrow<?php echo $selftest_code.$selftest_id.$selftest_user.$selftest_row; ?>" onclick='changeimage("<?php echo $selftest_code.$selftest_id.$selftest_user.$selftest_row; ?>");'></a>
					                   <?php echo $selftest_name;?>
					                  <img src="images/addtoright.png" class="addassignedplanselftests" align="right" style="height:20px;width:auto;cursor:pointer;background-color:#f2bd43;" id="<?php echo $selftest_code."~~".$selftest_id."~~".$selftest_user."~~".$selftest_row; ?>" title="Add to current plan">
					                </h4>
					              </div>
					              <div id="collapse<?php echo $selftest_code.$selftest_id.$selftest_user.$selftest_row; ?>" class="panel-collapse collapse plancreationpanelbody" role="tabpanel" aria-labelledby="heading<?php echo $selftest_code.$selftest_id.$selftest_row.$selftest_user; ?>">
					                <div class="panel-body"  style="text-align:center;">
					                	<?php
					                		echo $selftest_doc;
					                	?>
					                </div>
					              </div>
					            </div>
			    			<?php
			    		}
		    		} else {
		    			echo "<div style='color:#fff;'>No Self Tests to show</div>";
		    		}
			    		?>
		    		</div>
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
		<script type="text/javascript" src="js/bootbox.min.js"></script>
		<script type="text/javascript" src="js/common.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			$('#10').addClass('active');
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

        	var sidelistheight = $('#listBar').height();
        	var availableheight = sidelistheight - 280;
        	availableheight = availableheight/2;
        	//alert(availableheight);
        	$('.masterplanactivities').height(availableheight);
        	$('.assignedplanactivities').height(availableheight);

        	$('#thisplantitle').click(function(){
				$('#plandetailsmodal').modal('show');
			});
			$('#editthisplantitle').click(function(){
				$('#plandetailsmodal').modal('show');
			});
        	$('#plan_desc').keyup(function() {
				var text_length = $('#plan_desc').val().length;
				var text_remaining = text_max - text_length;

				$('#textarea_feedback').html(text_remaining + ' characters remaining');
			});
			$(document).on('focus', '.specificdate', function () {
				$(this).datepicker({
			        dateFormat: "dd-M-yy",
			        minDate: 0,
			        changeMonth: true,
			        changeYear: true,
			     });
			});
			setTimeout(function() {
		        $("#getselftests").trigger('click');
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

			$('#addItemButton, #getselftests').click(function(){
				$.ajax({
					type        : "GET",
					url			: "selfTestCustomizePage6.php",
					//url			: "stupid.php",
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
			$(document).on('change', '.testnameselect', function () {
				var testid = $(this).attr('id');
			   	var id  = testid.replace("selftestName", "");
     		    var value = this.value;
			   //alert(value);
			   if(value == 0){
			   	//alert(1);
			   	$('#selftestOther'+id).prop('disabled', false);
			   	$('#selftestOther'+id).css('opacity', '1');
			    $('#selftestOther'+id).focus();
			   }else {
			   	$('#selftestOther'+id).prop('disabled', true);
			   	$('#selftestOther'+id).css('opacity', '0.2');
			   	$('#doctorName'+id).focus();
			   }
			   	
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

				 	if(selftestname == "select"){
				 		// bootbox.alert("Please select name of self test");
					 	// 	$('.bootbox').on('hidden.bs.modal', function() { 
							//     $('#selftestName'+result[i]).focus();
							// });
							// return false;
				 	}
				 	else
				 	{
				 		if(selftestname == 0){
				 			var selftestothername = $('#selftestOther'+result[i]).val();
				 			if(selftestothername.replace(/\s+/g, '') == ""){
				 				bootbox.alert("Please enter the name of self test");
				 				$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#selftestOther'+result[i]).focus();
									});
									return false;
				 			}
						}
				 		numberOfSelfTests = numberOfSelfTests + 1;
				 		var current_doctor_name = $('#doctorName'+result[i]).val();
				 		//alert(current_doctor_name)
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
				 		//alert(wheninput)
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
							});
				 			return false;
				 		
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
				window.location.href = "customize_plan.php";
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
        		$('.addassignedplanselftests').click(function(){
			   var selfid 	= $(this).attr('id'); 
			   $("select").each(function() {
			    var id = $(this).attr("id");//id of the current textarea
			   // alert(id);
			    var deleted_row_id  = id.replace("selftestName", "");
			    if (id.indexOf("selftestName") >= 0){

			    var selftest_name = $(this).val();
			   	if(selftest_name.replace(/\s+/g, '') == 0){
			      $('#stslno tr:last').remove();
        			$(this).closest('tr').next().remove();//To remove the next tr
				      $(this).closest('tr').next().remove();//To remove the next tr
				      $(this).closest('tr').next().remove();//To remove the next tr
				      $(this).closest('tr').next().remove();//To remove the next tr
				      $(this).closest('tr').remove();
			     //alert(selftestcount);
			     var selftestcount = $('#selftestcount').val();
			      selftestcount = parseInt(selftestcount) - 1;
			      //alert(selftestcount);
			      if(selftestcount == 1){
			        $('.deleterow').hide();
			      }
			        var deleted_usedlabtest 		= deleted_row_id+",";
			        var current_usedselftestcount 	= $('#usedselftestcount').val();
			        var new_usedselftestcount  		= current_usedselftestcount.replace(deleted_usedlabtest, "");
			        $('#usedselftestcount').val(new_usedselftestcount);
			        $('#selftestcount').val(selftestcount);
			    }
			    }
			});
			var assigned 	= selfid.split("~~");
			var plancode 	= assigned[0];
			var selftestid 	= assigned[1];
			var rowno 		= assigned[3];
			var userid 		= assigned[2];
			//alert(prescno);
			var dataString = "plancode="+plancode+"&type=get_assigned_selftests&testid="+selftestid+"&rowno="+rowno+"&userid="+userid;
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
                      selftestcount = $('#selftestcount').val();
				        propercount     = $('#propercount').val();
				        selftestcount = parseInt(selftestcount) + 1;
				        propercount     = parseInt(propercount) + 1;
				         $('.deleterow').show();
				        var first = "<tr style='border-top:4px solid #004f35;'><td style='padding:5px;' align='center' colspan='2'><select name='selftestName"+propercount+"' id='selftestName"+propercount+"' style='width:100%;' class='testnameselect'><option style='display:none;' value='0'>Select Test Name</option>"+item.MedicalTestOptions+"</select></td> <td style='padding:5px;' align='center' colspan='2'><textarea rows='1' name='selftestOther"+propercount+"' id='selftestOther"+propercount+"' placeholder='Enter Self Test Name..' maxlength='100' title='Enter Self Test Name' "+item.TestNameStyle+">"+item.TestName+"</textarea></td> <td style='padding:5px;' align='center' colspan='3'><textarea rows='1' name='doctorName"+propercount+"' id='doctorName"+propercount+"' placeholder='Enter Doctor Name..' maxlength='25' style='height:40px;width:100%;'>"+item.DoctorsName+"</textarea></td><td style='width:300px;' style='padding:5px;'><input type='radio' name='radio"+propercount+"' value='PS' checked class='radio"+propercount+" selfriptionradio' "+item.PlanStartRadio+"> When the plan Starts/Updates<br></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"' title='Delete this row'></td></tr><tr><td style='padding:5px;' align='center'></td><td style='padding:5px;' align='left'>Instruction:</td><td style='padding:5px;' align='center'><select name='when"+propercount+"' id='when"+propercount+"'><option style='display:none;' value='0'>select</option>"+item.InstructionOptions+"</select></td><td style='padding:5px;' align='center'>Frequency:</td><td style='padding:5px;' align='center'><select name='frequency"+propercount+"' id='frequency"+propercount+"' title='Select the selfttest frequency' class='testfrequency'>"+item.FrequencyOptions+"</select></td><td style='padding:5px;' align='center'>Duration :</td><td style='padding:5px;' align='center'> <div style='border-radius:10px;padding:5px;background-color:#004f35;height:40px;width:150px;'><input type='text' maxlength='2' name='count"+propercount+"' id='count"+propercount+"' style='width:35px;height:30px;' class='forminputs2 roundedinputs countbox' title='Enter the duration' value='"+item.HowLong+"'><select id='countType"+propercount+"' name='countType"+propercount+"' style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;' title='Enter the duration'>"+item.HowLongTypeOptions+"</select></div></td><td><input type='radio' name='radio"+propercount+"' value='ND' class='radio"+propercount+" selfriptionradio'"+item.NumOfDaysRadio+"> No. Of days after plan started&nbsp;<input type='text' size='6px' name='numofdays"+propercount+"' id='numofdays"+propercount+"' class='numofdays roundedinputs "+item.NDClass+"' maxlength='2' value='"+item.NoOfDaysAfterPlanStarts+"'></td></tr><tr><td style='padding:5px;' align='center' colspan='7'><textarea rows='1' name='selftestdesc"+propercount+"' placeholder='Enter Requirements to selftest..' class='forminputs2' id='selftestdesc"+propercount+"' style='height:40px;width:100%;'>"+item.TestDescription+"</textarea></td><td><input type='radio' name='radio"+propercount+"' value='SD' class='radio"+propercount+" selfriptionradio'  "+item.SpecificDateRadio+"> On Specific Date:&nbsp;<input type='text' size='10px' name='specificdate"+propercount+"' id='specificdate"+propercount+"' class='specificdate "+item.SDClass+"' value='"+item.SpecificDate+"'></td></tr><tr><td><td style='padding:5px;'>Link :</td> <td colspan='5' style='padding:5px;'><input type='text' name='linkentered"+propercount+"' id='linkentered"+propercount+"' class='forminputs2' title='Enter Link here' placeholder='Enter Link Here (Optional)'></td><td colspan='2' style='padding:5px;'></td></tr><tr><td colspan='8'><input "+item.WeeklyType+" name='selectedweekdays"+propercount+"' id='selectedweekdays"+propercount+"' class='forminputs2 editselectedweekday' title='Click here to edit frequency' value='"+item.FrequencyString+"'><input "+item.MonthlyType+" name='selectedmonthdays"+propercount+"' id='selectedmonthdays"+propercount+"' class='forminputs2 editselectedmonthday' title='Click here to edit frequency' value='"+item.FrequencyString+"'><div class='bottomshadow' style='width:100%;color:#fff;pointer-events:none;height:12px;'><span style='display:none;'>1</span></div></td></tr>";
				        var slno  = "<tr><td>"+selftestcount+"</td></tr>";
				        $('#stslno > tbody').append(slno);
				        $('#pdata').append(first);
				        var current_usedselftestcount = $('#usedselftestcount').val();
				        var new_usedselftestcount = current_usedselftestcount+propercount+",";
				        $('#usedselftestcount').val(new_usedselftestcount);
				        $('#selftestcount').val(selftestcount);
				        $('#propercount').val(propercount);
                    });
                  },
                  error: function(){

                  }
                });

		});
		$('.addmasterplanselftests').click(function(){
			   var selfid 	= $(this).attr('id'); 
			   $("select").each(function() {
			    var id = $(this).attr("id");//id of the current textarea
			   // alert(id);
			    var deleted_row_id  = id.replace("selftestName", "");
			    if (id.indexOf("selftestName") >= 0){

			    var selftest_name = $(this).val();
			    //alert(selftest_name);
			   	if(selftest_name.replace(/\s+/g, '') == 0){
			      $('#stslno tr:last').remove();
        			$(this).closest('tr').next().remove();//To remove the next tr
				      $(this).closest('tr').next().remove();//To remove the next tr
				      $(this).closest('tr').next().remove();//To remove the next tr
				      $(this).closest('tr').next().remove();//To remove the next tr
				      $(this).closest('tr').remove();
			     //alert(selftestcount);
			     var selftestcount = $('#selftestcount').val();
			      selftestcount = parseInt(selftestcount) - 1;
			      //alert(selftestcount);
			      if(selftestcount == 1){
			        $('.deleterow').hide();
			      }
			        var deleted_usedlabtest 		= deleted_row_id+",";
			        var current_usedselftestcount 	= $('#usedselftestcount').val();
			        var new_usedselftestcount  		= current_usedselftestcount.replace(deleted_usedlabtest, "");
			        $('#usedselftestcount').val(new_usedselftestcount);
			        $('#selftestcount').val(selftestcount);
			    }
			    }
			});

			
			//alert(selfid);
			var master 		= selfid.split("~~");
			var plancode 	= master[0];
			var selftestid 	= master[1];
			var rowno 		= master[2];
			var dataString 	= "plancode="+plancode+"&type=get_master_selftests&testid="+selftestid+"&rowno="+rowno;
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
                      selftestcount = $('#selftestcount').val();
				        propercount     = $('#propercount').val();
				        selftestcount = parseInt(selftestcount) + 1;
				        propercount     = parseInt(propercount) + 1;
				         $('.deleterow').show();
				        var first = "<tr style='border-top:4px solid #004f35;'><td style='padding:5px;' align='center' colspan='2'><select name='selftestName"+propercount+"' id='selftestName"+propercount+"' style='width:100%;' class='testnameselect'><option style='display:none;' value='0'>Select Test Name</option>"+item.MedicalTestOptions+"</select></td> <td style='padding:5px;' align='center' colspan='2'><textarea rows='1' name='selftestOther"+propercount+"' id='selftestOther"+propercount+"' placeholder='Enter Self Test Name..' maxlength='100' title='Enter Self Test Name' "+item.TestNameStyle+">"+item.TestName+"</textarea></td> <td style='padding:5px;' align='center' colspan='3'><textarea rows='1' name='doctorName"+propercount+"' id='doctorName"+propercount+"' placeholder='Enter Doctor Name..' maxlength='25' style='height:40px;width:100%;'>"+item.DoctorsName+"</textarea></td><td style='width:300px;' style='padding:5px;'><input type='radio' name='radio"+propercount+"' value='PS' checked class='radio"+propercount+" selfriptionradio' "+item.PlanStartRadio+"> When the plan Starts/Updates<br></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"' title='Delete this row'></td></tr><tr><td style='padding:5px;' align='center'></td><td style='padding:5px;' align='left'>Instruction:</td><td style='padding:5px;' align='center'><select name='when"+propercount+"' id='when"+propercount+"'><option style='display:none;' value='0'>select</option>"+item.InstructionOptions+"</select></td><td style='padding:5px;' align='center'>Frequency:</td><td style='padding:5px;' align='center'><select name='frequency"+propercount+"' id='frequency"+propercount+"' title='Select the selfttest frequency' class='testfrequency'>"+item.FrequencyOptions+"</select></td><td style='padding:5px;' align='center'>Duration :</td><td style='padding:5px;' align='center'> <div style='border-radius:10px;padding:5px;background-color:#004f35;height:40px;width:150px;'><input type='text' maxlength='2' name='count"+propercount+"' id='count"+propercount+"' style='width:35px;height:30px;' class='forminputs2 roundedinputs countbox' title='Enter the duration' value='"+item.HowLong+"'><select id='countType"+propercount+"' name='countType"+propercount+"' style='width:100px;float:right;height:30px;line-height:25px;background-color:#2B6D57;' title='Enter the duration'>"+item.HowLongTypeOptions+"</select></div></td><td><input type='radio' name='radio"+propercount+"' value='ND' class='radio"+propercount+" selfriptionradio'"+item.NumOfDaysRadio+"> No. Of days after plan started&nbsp;<input type='text' size='6px' name='numofdays"+propercount+"' id='numofdays"+propercount+"' class='numofdays roundedinputs "+item.NDClass+"' maxlength='2' value='"+item.NoOfDaysAfterPlanStarts+"'></td></tr><tr><td style='padding:5px;' align='center' colspan='7'><textarea rows='1' name='selftestdesc"+propercount+"' placeholder='Enter Requirements to selftest..' class='forminputs2' id='selftestdesc"+propercount+"' style='height:40px;width:100%;'>"+item.TestDescription+"</textarea></td><td><input type='radio' name='radio"+propercount+"' value='SD' class='radio"+propercount+" selfriptionradio'  "+item.SpecificDateRadio+"> On Specific Date:&nbsp;<input type='text' size='10px' name='specificdate"+propercount+"' id='specificdate"+propercount+"' class='specificdate "+item.SDClass+"' value='"+item.SpecificDate+"'></td></tr><tr><td><td style='padding:5px;'>Link :</td> <td colspan='5' style='padding:5px;'><input type='text' name='linkentered"+propercount+"' id='linkentered"+propercount+"' class='forminputs2' title='Enter Link here' placeholder='Enter Link Here (Optional)'></td><td colspan='2' style='padding:5px;'></td></tr><tr><td colspan='8'><input "+item.WeeklyType+" name='selectedweekdays"+propercount+"' id='selectedweekdays"+propercount+"' class='forminputs2 editselectedweekday' title='Click here to edit frequency' value='"+item.FrequencyString+"'><input "+item.MonthlyType+" name='selectedmonthdays"+propercount+"' id='selectedmonthdays"+propercount+"' class='forminputs2 editselectedmonthday' title='Click here to edit frequency' value='"+item.FrequencyString+"'><div class='bottomshadow' style='width:100%;color:#fff;pointer-events:none;height:12px;'><span style='display:none;'>1</span></div></td></tr>";
				        var slno  = "<tr><td>"+selftestcount+"</td></tr>";
				        $('#stslno > tbody').append(slno);
				        $('#pdata').append(first);
				        var current_usedselftestcount = $('#usedselftestcount').val();
				        var new_usedselftestcount = current_usedselftestcount+propercount+",";
				        $('#usedselftestcount').val(new_usedselftestcount);
				        $('#selftestcount').val(selftestcount);
				        $('#propercount').val(propercount);
                    });
                  },
                  error: function(){

                  }
                });
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