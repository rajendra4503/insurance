<?php
session_start();
//ini_set("display_errors","0");
//echo "<pre>"; print_r($_SESSION);exit;

include('include/configinc.php');
include('include/session.php');
include('include/functions.php');

//$_SESSION['plancode_for_current_plan'] = "IN0000000030";
$plancode_for_current_plan = $_SESSION['plancode_for_current_plan'];
$planname_for_current_plan_text = "Click to edit Plan Details";


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
if(((isset($_REQUEST['goalDescription1']))&&(!empty($_REQUEST['goalDescription1'])))||((isset($_REQUEST['goalDescription2']))&&(!empty($_REQUEST['goalDescription2'])))){
	//echo "<pre>";print_r($_POST);exit;
	$goalDescription1 	= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['goalDescription1'])));
	$goal1tabs 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['goal1tabs'])));
	$goalDescription2 	= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['goalDescription2'])));
	$goal2tabs 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['goal2tabs'])));
	$delete_existing = mysql_query("delete from GOAL_DETAILS where PlanCode='$plancode_for_current_plan'");
	if($goalDescription1 != ""){
		$insert_goal_details = mysql_query("insert into GOAL_DETAILS (`PlanCode`, `GoalNo`, `GoalDescription`, `DisplayedWith`, `CreatedDate`, `CreatedBy`) values ('$plancode_for_current_plan', '1', '$goalDescription1', '$goal1tabs', now(), '$logged_userid')");
		//echo "insert into GOAL_DETAILS (`PlanCode`, `GoalNo`, `GoalDescription`, `DisplayedWith`, `CreatedDate`, `CreatedBy`) values ('$plancode_for_current_plan', '1', '$goalDescription1', '$goal1tabs', now(), '$logged_userid')";exit;
	}
	if($goalDescription2 != ""){
		$insert_goal_details = mysql_query("insert into GOAL_DETAILS (`PlanCode`, `GoalNo`, `GoalDescription`, `DisplayedWith`, `CreatedDate`, `CreatedBy`) values ('$plancode_for_current_plan', '2', '$goalDescription2', '$goal2tabs', now(), '$logged_userid')");
	}
			$check_affected_rows= mysql_affected_rows();
		        if($check_affected_rows)
		       {
		       	header("Location:plan_goals.php");
		       } else {
		       	header("Location:plan_goals.php");
		       }

}
//Get Goals
$get_goal = "select PlanCode, GoalNo, GoalDescription, DisplayedWith, CreatedDate from GOAL_DETAILS where PlanCode='$plancode_for_current_plan'";
  //echo $get_presc;exit;
  $get_goal_run = mysql_query($get_goal);
  $get_goal_count = mysql_num_rows($get_goal_run);
  $goalDescription1 = "";
  $goal1tabs 		= "";
  $goalDescription2 = "";
  $goal2tabs 		= "";
  $goal1tabstext 	= "";
  $goal2tabstext 	= "";
  $tabnames = array("1" => "Medication", "2" => "Appointment", "3-2" => "Lab Test", "8" => "Instruction", "3-1" => "Self Test");
  if($get_goal_count >0){
	  while ($goals = mysql_fetch_array($get_goal_run)) {
	  	$GoalNo = $goals['GoalNo'];
	  	if($get_goal_count > 1){
		  	if($GoalNo == "1"){
			  $goalDescription1 = $goals['GoalDescription'];
			  $goal1tabstext 	= "Displayed With : ";
			  $goal1tabs 		= $goals['DisplayedWith'];	
			  $tabexplode 		= explode(",", $goal1tabs);
			  foreach ($tabexplode as $tab) {
			  	if($tab != ""){
			  		$goal1tabstext .= $tabnames[$tab].", ";
			  	}
			  }
		  	}
		  	if($GoalNo == "2"){
		  	  $goal2tabstext 	= "Displayed With : ";
			  $goalDescription2 = $goals['GoalDescription'];
			  $goal2tabs 		= $goals['DisplayedWith'];		
			  $tabexplode 		= explode(",", $goal2tabs);
			  foreach ($tabexplode as $tab) {
			  	if($tab != ""){
			  		$goal2tabstext .= $tabnames[$tab].", ";
			  	}
			  }
		  	}	  		
	  	} else {
	  		  $goal1tabstext 	= "Displayed With : ";
		  	  $goalDescription1 = $goals['GoalDescription'];
			  $goal1tabs 		= $goals['DisplayedWith'];	
			  $tabexplode 		= explode(",", $goal1tabs);
			  foreach ($tabexplode as $tab) {
			  	if($tab != ""){
			  		$goal1tabstext .= $tabnames[$tab].", ";
			  	}
			  }	
	  	}
	  }  	
  }

?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Plan Piper | Goals</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/jasny-bootstrap.min.css">
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
    		<h6 style="font-family:Freestyle;font-size:33px;margin-top:-1px;letter-spacing:1px;color:#f2bd43;background-color:#000;">Goals List</h6>
    		<div class="sidebarheadings">Master Goals :</div>
    				    		<div class="panel-group masterplanactivities" id="accordion1" role="tablist" aria-multiselectable="true" style="max-height:250px;overflow:scroll;overflow-x:hidden;">
			    		<?php
			    		$get_plan_goals 		= "select t1.GoalDescription, t1.PlanCode, t1.GoalNo, t1.CreatedDate from GOAL_DETAILS as t1,PLAN_HEADER as t2 where t1.PlanCode=t2.PlanCode and t2.MerchantID='$logged_merchantid' and t2.PlanStatus='A'";
			    		//echo $get_plan_goals;exit;
		    		$get_plan_goals_run 	= mysql_query($get_plan_goals);
		    		$get_plan_goals_count 	= mysql_num_rows($get_plan_goals_run);
		    		if($get_plan_goals_count > 0){
			    		while ($get_goals_row = mysql_fetch_array($get_plan_goals_run)) {
			    			$goaldescription   	= $get_goals_row['GoalDescription'];
			    			$goaldescriptionfull   	= $get_goals_row['GoalDescription'];
			    			$length 			= strlen($goaldescription);
			    			if($length > 12){
			    				$goaldescription 	= substr($goaldescription,0,12);
			    				$goaldescription 	= $goaldescription."...";
			    			}
			    			$goal_no   = $get_goals_row['GoalNo'];
			    			$goal_code = $get_goals_row['PlanCode'];
			    			$goal_date = date('d-M-Y',strtotime($get_goals_row['CreatedDate']));
			    			?>
			    			 <div class="panel panel-default plancreationpanel">
					              <div class="panel-heading plancreationaccordion" role="tab" id="heading<?php echo $goal_code.$goal_no; ?>">
					                <h4 class="panel-title" style="text-align:center;">
					                  <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $goal_code.$goal_no; ?>" aria-expanded="false" aria-controls="collapse"><img src="images/rightarrow.png" style="height:12px;width:auto;" align="left" id="arrow<?php echo $goal_code.$goal_no; ?>" onclick='changeimage("<?php echo $goal_code.$goal_no; ?>");'></a>
					                   <span title='<?php echo $goaldescriptionfull;?>'><?php echo $goaldescription;?></span>
					                  <img src="images/addtoright.png" class="addmasterplangoals" align="right" style="height:20px;width:auto;cursor:pointer;background-color:#f2bd43;" id="<?php echo $goal_code."~~".$goal_no; ?>" title="Add to current plan">
					                </h4>
					              </div>
					              <div id="collapse<?php echo $goal_code.$goal_no; ?>" class="panel-collapse collapse plancreationpanelbody" role="tabpanel" aria-labelledby="heading<?php echo $goal_code.$goal_no; ?>">
					                <div class="panel-body"  style="text-align:center;">
					                	<?php echo $goaldescriptionfull;?>
					                </div>
					              </div>
					            </div>
			    			<?php
			    		}
		    		}else {
		    			echo "<div style='color:#fff;'>No Goals to show</div>";
		    		}
			    		?>
		    		</div>
		    		<div class="sidebarheadings" style="margin-top: -13px;">Assigned Goals :</div>
		    				    		<div class="panel-group assignedplanactivities" id="accordion2" role="tablist" aria-multiselectable="true" style="overflow:scroll;overflow-x:hidden;">
			    		<?php
			    		$get_plan_goals 		= "select t1.UserID,t1.GoalDescription, t1.PlanCode, t1.GoalNo, t1.CreatedDate from USER_GOAL_DETAILS as t1,USER_PLAN_HEADER as t2 
			    		where t1.PlanCode=t2.PlanCode and t1.UserID=t2.UserID and t2.MerchantID='$logged_merchantid'";
		    		$get_plan_goals_run 	= mysql_query($get_plan_goals);
		    		$get_plan_goals_count 	= mysql_num_rows($get_plan_goals_run);
		    		if($get_plan_goals_count > 0){
			    		while ($get_goals_row = mysql_fetch_array($get_plan_goals_run)) {
			    			$goaldescription 	= $get_goals_row['GoalDescription'];
			    			$goaldescriptionfull = $get_goals_row['GoalDescription'];
			    			$fortitle			= $get_goals_row['GoalDescription'];
			    			$length 			= strlen($goaldescription);
			    			if($length > 12){
			    				$goaldescription 	= substr($goaldescription,0,12);
			    				$goaldescription 	= $goaldescription."...";
			    			}
			    			$goal_no   = $get_goals_row['GoalNo'];
			    			$goal_user   = $get_goals_row['UserID'];
			    			$goal_code = $get_goals_row['PlanCode'];
			    			$goal_date = date('d-M-Y',strtotime($get_goals_row['CreatedDate']));
			    			?>
			    			 <div class="panel panel-default plancreationpanel">
					              <div class="panel-heading plancreationaccordion" role="tab" id="heading<?php echo $goal_user.$goal_code.$goal_no; ?>">
					                <h4 class="panel-title" style="text-align:center;">
					                  <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $goal_user.$goal_code.$goal_no; ?>" aria-expanded="true" aria-controls="collapse<?php echo $goal_user.$goal_code.$goal_no; ?>"><img src="images/rightarrow.png" style="height:12px;width:auto;cursor:pointer;" align="left"></a>
					                   <span title='<?php echo $goaldescriptionfull;?>'><?php echo $goaldescription;?></span>
					                  <img src="images/addtoright.png" class="addassignedplangoals" align="right" style="height:20px;width:auto;cursor:pointer;background-color:#f2bd43;" id="<?php echo $goal_user."~~".$goal_code."~~".$goal_no; ?>" title="Add to current plan">
					                </h4>
					              </div>
					              <div id="collapse<?php echo $goal_user.$goal_code.$goal_no; ?>" class="panel-collapse collapse plancreationpanelbody" role="tabpanel" aria-labelledby="heading<?php echo $goal_user.$goal_code.$goal_no; ?>">
					                <div class="panel-body" style="text-align:center;">
					                	<?php
					                		echo $goaldescriptionfull;
					                	?>

					                </div>
					              </div>
					            </div>
			    			<?php
			    		}
		    		}else {
		    			echo "<div style='color:#fff;'>No Goals to show</div>";
		    		}
			    		?>
		    		</div>
		    </div>
		    </div>
    	</div>
    	<div class="col-lg-10 col-md-9 col-sm-9 col-sm-12 maincontent" style="padding-bottom:100px;">
	    	<div>
	    	<!-- <div class="topnotetext well">
	    		Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
	    	</div> -->
	    	<form name="frm_plan_goals" id="frm_plan_goals" method="POST">
	    		<table id="pdata" class="table" style="border:transparent;">
	    			<tr>
	    				<td>#</td>
	    				<td>Goal</td>
	    				<td align="center">Displayed With</td>
	    			</tr>
	    			<tr>
	    				<td>1</td>
	    				<td><input type="text" name="goalDescription1" id="goalDescription1" placeholder="Enter the Goal.." class="forminputs2" maxlength="50" title="Enter a goal for this plan" value="<?php echo $goalDescription1;?>"></td>
	    				<td align="center"><div class="btns goalbutton tabpickerbutton" id="1">Click to Select</div></td>
	    			</tr>
	    			<tr>
	    				<td colspan="3" align="center"><input type="hidden" name="goal1tabs" id="goal1tabs" value="<?php echo $goal1tabs;?>"><input type="text" name="goal1tabstext" id="goal1tabstext" value="<?php echo $goal1tabstext;?>" readonly></td>
	    			</tr>
	    			<tr>
	    				<td>2</td>
	    				<td><input type="text" name="goalDescription2" id="goalDescription2" placeholder="Enter the Goal.." class="forminputs2" maxlength="50" title="Enter a goal for this plan" value="<?php echo $goalDescription2;?>"></td>
	    				<td align="center"><div class="btns goalbutton tabpickerbutton" id="2">Click to Select</div></td>
	    			</tr>
	    			<tr>
	    				<td colspan="3" align="center"><input type="hidden" name="goal2tabs" id="goal2tabs" value="<?php echo $goal2tabs;?>"><input type="text" name="goal2tabstext" id="goal2tabstext" value="<?php echo $goal2tabstext;?>" readonly></td>
	    			</tr>
	    			<tr>
	    				<td colspan="3" align="center"><div type="button" id="saveAndEdit" class="btns goalbutton formbuttons" style="margin-top:20px;height:40px;line-height:40px;padding:0px;">SAVE</div></td>
	    			</tr>
	    		</table>	    		
	    	</form>

	    	</div>
    	</div>
    </div>
	</div>	

     <!--SHOW MONTH DAY PICKER MODAL WINDOW-->
            <div class="modal" id="goaltabpicker" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header padding0" align="center" style="border:none;">
                   <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                  <h5 class="modal-title" id="modalheadings">Select</h5>
                  </div>
                  <div class="modal-body taboptions" align="center" style="padding-top:0px;background-color:#fff;padding-bottom:50px;">

               	  </div>
               	  <div class="margin10" align="center">
               	  <input type="hidden" name="tabselectedid" id="tabselectedid" value="0">
               	  <button class="smallbutton" id="tabsselected">Done</button>
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
		<script type="text/javascript" src="js/placeholders.min.js"></script>
		<script type="text/javascript" src="js/bootbox.min.js"></script>
		<script type="text/javascript" src="js/jasny-bootstrap.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			$('#11').addClass('active');
			mainHeader = 115;//include main header height also
			plantitleHeight = $("#plantitle").outerHeight(true);//true includes margin height also
			navigationbarHeight = $("#navigationbar").outerHeight(true);//true includes margin height also
			totalUsedHeight = plantitleHeight + navigationbarHeight + mainHeader;
			listBarHeight = ($(window).innerHeight()-totalUsedHeight);
		  	//$('#listBar').css({height: listBarHeight});
		  	//$('#dynamicPagePlusActionBar').css({height: listBarHeight});

		  	var h = window.innerHeight;
		  	var windowheight = h;
       		var available_height = h - 210;
        	//$('.maincontent').height(available_height);
        	var sidelistheight = $('#listBar').height();
        	var availableheight = sidelistheight - 280;
        	availableheight = availableheight/2;
        	//alert(availableheight);
        	$('.masterplanactivities').height(availableheight);
        	$('.assignedplanactivities').height(availableheight);
			var medicationcount = 0;
			$('#plapiper_pagename').html("Goals");

			$('#thisplantitle').click(function(){
				$('#plandetailsmodal').modal('show');
			});
			$('#editthisplantitle').click(function(){
				$('#plandetailsmodal').modal('show');
			});
			var text_max = 499;
			$('#textarea_feedback').html(text_max + ' characters remaining');

			$('#plan_desc').keyup(function() {
				var text_length = $('#plan_desc').val().length;
				var text_remaining = text_max - text_length;

				$('#textarea_feedback').html(text_remaining + ' characters remaining');
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
			var alltabs = ["1","2","3-2","8","3-1"];
			var tabtexts = [];
			tabtexts["1"] = "Medication";
			tabtexts["2"] = "Appointment";
			tabtexts["3-2"] = "Lab Test";
			tabtexts["8"] = "Instruction";
			tabtexts["3-1"] = "Self Test";
			var usedtabs = [];
			var goal1tabs = $('#goal1tabs').val().split(',');
			var goal2tabs = $('#goal2tabs').val().split(',');
			var diff 		= $(alltabs).not(goal1tabs).get(); //In alltabs , not in goal1tabs
			var availabletabs = $(diff).not(goal2tabs).get();//In diff , not in goal2tabs
			//var availabletabs = ["1","2","3-2","8","3-1"];
			$('.tabpickerbutton').click(function(){
				var tabid  = $(this).attr('id');
				var selectedtabs = [];
				if(tabid == "1"){
					var i;
					for (i = 0; i < goal1tabs.length; ++i) {
					    selectedtabs.push(goal1tabs[i]);
					}
				}else {
					var i;
					for (i = 0; i < goal2tabs.length; ++i) {
					    selectedtabs.push(goal2tabs[i]);
					}
				}
				var taboptions = "<div class='btn-group' data-toggle='buttons' align='center'>";
				if(($.inArray("1", availabletabs) !== -1)||($.inArray("1", selectedtabs) !== -1)){
					taboptions = taboptions + "<label class='btn btn-primary width150px'><input type='checkbox' autocomplete='off' name='tabcheck' class='tabcheck' value='1'> Medication</label>";
				}else {
					taboptions = taboptions + "<label class='btn btn-primary disabled width150px'><input type='checkbox' autocomplete='off' disabled name='tabcheck' class='tabcheck' value='1'> Medication</label>";
				}
				if(($.inArray("2", availabletabs) !== -1)||($.inArray("2", selectedtabs) !== -1)){
					taboptions = taboptions + "<label class='btn btn-primary width150px'><input type='checkbox' autocomplete='off' name='tabcheck' class='tabcheck' value='2'> Appointment</label>";
				}else {
					taboptions = taboptions + "<label class='btn btn-primary disabled width150px'><input type='checkbox' autocomplete='off' disabled name='tabcheck' class='tabcheck' value='2'> Appointment</label>";
				}
				if(($.inArray("3-2", availabletabs) !== -1)||($.inArray("3-2", selectedtabs) !== -1)){
					taboptions = taboptions + "<label class='btn btn-primary width150px'><input type='checkbox' autocomplete='off' name='tabcheck' class='tabcheck' value='3-2'> Lab Test</label>";
				}else {
					taboptions = taboptions + "<label class='btn btn-primary disabled width150px'><input type='checkbox' autocomplete='off' disabled name='tabcheck' class='tabcheck' value='3-2'> Lab Test</label>";
				}
				if(($.inArray("8", availabletabs) !== -1)||($.inArray("8", selectedtabs) !== -1)){
					taboptions = taboptions + "<label class='btn btn-primary width150px'><input type='checkbox' autocomplete='off' name='tabcheck' class='tabcheck' value='8'> Instruction</label>";
				}else {
					taboptions = taboptions + "<label class='btn btn-primary disabled width150px'><input type='checkbox' autocomplete='off' disabled name='tabcheck' class='tabcheck' value='8'> Instruction</label>";
				}
				if(($.inArray("3-1", availabletabs) !== -1)||($.inArray("3-1", selectedtabs) !== -1)){
					taboptions = taboptions + "<label class='btn btn-primary width150px'><input type='checkbox' autocomplete='off' name='tabcheck' class='tabcheck' value='3-1'> Self Test</label>";
				}else {
					taboptions = taboptions + "<label class='btn btn-primary disabled width150px'><input type='checkbox' autocomplete='off' disabled name='tabcheck' class='tabcheck' value='3-1'> Self Test</label>";
				}
				taboptions = taboptions + "</div>";
				$('#tabselectedid').val(tabid);
				$('.taboptions').html(taboptions);
				if(tabid == "1"){
					$('.tabcheck').each(function() {
						if(jQuery.inArray($(this).val(),goal1tabs) != -1){
	                    	$(this).prop('checked',true);
	                    	$(this).parents('label').addClass('active');
	                    }
                	});
				} else {
					$('.tabcheck').each(function() {
						if(jQuery.inArray($(this).val(),goal2tabs) != -1){
	                    	$(this).prop('checked',true);
	                    	$(this).parents('label').addClass('active');
	                    }
                	});
				}
				
				$('#goaltabpicker').modal('show');
			});
			setTimeout(function() {
				$("#getmedications").trigger('click');        
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
			$('.panel-heading a').on('click',function(e){
				var id = $(this).attr('href');
				if($(id).hasClass('in')){	
				} else {
					$(id).addClass('in');
				    $('.panel-collapse').removeClass('in');		
				}
			});
			//ON CLICK OF DONE BUTTON AFTER SELECTING THE TABS
			$(document).on('click', '#tabsselected', function () {
				//alert(1);
				var goal1tabstext = "";
				var goal2tabstext = "";
				var getcurrentid = $('#tabselectedid').val();
				if(getcurrentid == "1"){
					var i;
					for (i = 0; i < goal1tabs.length; ++i) {
					    availabletabs.push(goal1tabs[i]);
					}
					goal1tabs = [];
				}else {
					var i;
					for (i = 0; i < goal2tabs.length; ++i) {
					    availabletabs.push(goal2tabs[i]);
					}
					goal2tabs = [];
				}
				//alert(getcurrentid);
				var chkId2 = "";
				goal1tabstext = "Displayed With : ";
				goal2tabstext = "Displayed With : ";
				$('.tabcheck:checked').each(function() {
					if(getcurrentid == "1"){
						goal1tabs.push($(this).val());
						var index = availabletabs.indexOf($(this).val());
						availabletabs.splice(index, 1);
						goal1tabstext = goal1tabstext + tabtexts[$(this).val()] + ", ";
					}
					if(getcurrentid == "2"){
						goal2tabs.push($(this).val());
						var index = availabletabs.indexOf($(this).val());
						availabletabs.splice(index, 1);
						goal2tabstext = goal2tabstext + tabtexts[$(this).val()] + ", ";
					}

				  chkId2 += $(this).val() + ",";
				});
				if(chkId2 == ""){
					bootbox.alert("Please select atleast one option to continue..");
					return false;
				}
				//alert(chkId2);
				if(getcurrentid == "1"){
					$('#goal1tabs').val(chkId2);
					$('#goal1tabstext').val(goal1tabstext);
				}
				if(getcurrentid == "2"){
					$('#goal2tabs').val(chkId2);
					$('#goal2tabstext').val(goal2tabstext);
				}
				$('#goaltabpicker').modal('hide');
			});
				//ON CLICK OF SAVE BUTTON
				$(document).on('click', '#saveAndEdit', function () {
					var goalDescription1 = $('#goalDescription1').val();
					var goalDescription2 = $('#goalDescription2').val();
			    	if((goalDescription1.replace(/\s+/g, '') == "") && (goalDescription2.replace(/\s+/g, '') == "")){
			    		bootbox.alert("Please enter atleast one goal to continue.");
						$('.bootbox').on('hidden.bs.modal', function() {
								    $('#goalDescription1').focus();
								});
						return false;
					}
					
				if(goalDescription1.replace(/\s+/g, '') != ""){
					var goal1tabs = $('#goal1tabs').val();
					if(goal1tabs.replace(/\s+/g, '') == ""){
						bootbox.alert("Please select where the goal should be displayed.");
						$('.bootbox').on('hidden.bs.modal', function() {
								    $("#1.tabpickerbutton").click();
								});
						return false;
					}
				}
				if(goalDescription2.replace(/\s+/g, '') != ""){
					var goal2tabs = $('#goal2tabs').val();
					if(goal2tabs.replace(/\s+/g, '') == ""){
						bootbox.alert("Please select where the goal should be displayed.");
						$('.bootbox').on('hidden.bs.modal', function() {
								    $("#2.tabpickerbutton").click();
								});
						return false;
					}
				}

				$('#frm_plan_goals').submit();
			});
		$('.addassignedplangoals').click(function(){
			var goalid 	= $(this).attr('id'); 
			var assigned 	= goalid.split("~~");
			var userid 		= assigned[0];
			var plancode 	= assigned[1];
			var goalno 		= assigned[2];
			var merchantid  = '<?php echo $logged_merchantid;?>';
			var goalDescription1 = $('#goalDescription1').val();
			var goalDescription2 = $('#goalDescription2').val();
	    	if((goalDescription1.replace(/\s+/g, '') == "") || (goalDescription2.replace(/\s+/g, '') == "")){
				var dataString = "plancode="+plancode+"&type=get_assigned_goals&goalno="+goalno+"&userid="+userid+"&merchantid="+merchantid;
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
	                    	if(goalDescription1.replace(/\s+/g, '') == ""){
	                    		$('#goalDescription1').val(item.GoalDescription);
	                    		// $('#goal1tabs').val(item.DisplayedWith);
	                    	} else {
	                    		$('#goalDescription2').val(item.GoalDescription);
	                    		// $('#goal2tabs').val(item.DisplayedWith);
	                    	}
	                    });
	                  },
	                  error: function(){

	                  }
	                });
	    	 }else {
				bootbox.alert("A Plan can have only two goals.");
				return false;
			}


		});
		$('.addmasterplangoals').click(function(){
			var goalid 	= $(this).attr('id'); 
			var master 		= goalid.split("~~");
			var plancode 	= master[0];
			var goalno  	= master[1];
			var goalDescription1 = $('#goalDescription1').val();
			var goalDescription2 = $('#goalDescription2').val();
	    	if((goalDescription1.replace(/\s+/g, '') == "") || (goalDescription2.replace(/\s+/g, '') == "")){
				var merchantid  = '<?php echo $logged_merchantid;?>';
				var dataString = "plancode="+plancode+"&type=get_master_goals&goalno="+goalno+"&merchantid="+merchantid;
					//alert(dataString);
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
	                    	if(goalDescription1.replace(/\s+/g, '') == ""){
	                    		$('#goalDescription1').val(item.GoalDescription);
	                    		// $('#goal1tabs').val(item.DisplayedWith);
	                    	} else {
	                    		$('#goalDescription2').val(item.GoalDescription);
	                    		// $('#goal2tabs').val(item.DisplayedWith);
	                    	}
	                    });
	                  },
	                  error: function(){

	                  }
	                });
			} else {
				bootbox.alert("A Plan can have only two goals.");
				return false;
			}


		});
		$(document).on('change', '.criticalcheck', function () {
		   if($(this).is(":checked")) {
		      //bootbox.alert(1);
		      var criticalid 	= $(this).attr('id');
		      var id  			= criticalid.replace("critical", "");
		      $( "#response"+id ).prop( "checked", true );
		      //bootbox.alert(id);
		      $('#threshold'+id).prop('disabled', false);
				$('#threshold'+id).css('opacity', '1');
				$('#threshold'+id).focus();
		      return;
		   } else {
		   	var criticalid = $(this).attr('id');
		      var id  = criticalid.replace("critical", "");
		      $( "#response"+id ).prop( "checked", false );
		      //bootbox.alert(id);
		      $('#threshold'+id).prop('disabled', true);
				$('#threshold'+id).css('opacity', '0.2');
		      return;
		   }

		});
				$(document).on('change', '.responsecheck', function () {
		   if($(this).is(":checked")) {
		   	var responseid 	= $(this).attr('id');
		      var id  			= responseid.replace("response", "");
		      $('#threshold'+id).prop('disabled', false);
				$('#threshold'+id).css('opacity', '1');
				$('#threshold'+id).focus();
		      return;
		   } else {
		   	var responseid 	= $(this).attr('id');
		      var id  			= responseid.replace("response", "");
		      $('#threshold'+id).prop('disabled', true);
				$('#threshold'+id).css('opacity', '0.2');
		      return;
		   }

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
