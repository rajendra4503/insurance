<?php
session_start();
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
	//DELETE LAB TESTS

	$labtestcount    			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['labtestcount']))); //Total number of lab tests present on screen
	$usedlabtestcount 			= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST['usedlabtestcount']))); //row ids of medicines present
	$labttestids 				= explode(",", $usedlabtestcount);
	$current_labtest_num 		= "1";
	$lab_edit_id = "1";
	if($labtestcount > '0'){
		$delete_user_header = mysql_query("delete from LAB_TEST_HEADER1 where  PlanCode = '$plancode_for_current_plan' and LabTestID='$lab_edit_id'");
		$delete_user_details = mysql_query("delete from LAB_TEST_DETAILS1 where  PlanCode = '$plancode_for_current_plan' and LabTestID='$lab_edit_id'");	
	}
	$insert_header_details 	= " insert into LAB_TEST_HEADER1 (Plancode, LabTestID, CreatedDate, CreatedBy) values ('$plancode_for_current_plan', '$current_labtest_num', now(), '$logged_userid')";
	//echo $insert_header_details;exit;
	$insert_header_run  	= mysql_query($insert_header_details);
    $count = 1;
	foreach ($labttestids as $ids) {
		if($ids != ""){
			$labtestName = mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["labtestName$ids"])));
			if($labtestName != ""){
			$doctor_name 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["doctorName$ids"])));
			$requirements 				= mysql_real_escape_string(trim(htmlspecialchars($_REQUEST["requirements$ids"])));
			$insert_labttest_details = "insert into LAB_TEST_DETAILS1 (`PlanCode`,`LabTestID`,`RowNo`,`TestName`,`DoctorsName`,`LabTestRequirements`,`CreatedDate`,`CreatedBy`) values ('$plancode_for_current_plan', '$current_labtest_num', '$count', '$labtestName','$doctor_name','$requirements', now(), '$logged_userid');";
			//echo $insert_labttest_details;exit;
			$insert_header_run  	= mysql_query($insert_labttest_details);
			$check_affected_rows= mysql_affected_rows();
		        if($check_affected_rows)
		       { 
		       	header("Location:plan_lab_new.php");
		       } else {
		       	header("Location:plan_lab_new.php");
		       }
		}
	}
		$count++;
	}
	//$update_plan_header = mysql_query("update PLAN_HEADER set PlanStatus = 'A' where Plancode='$plancode_for_current_plan'");
}
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Plan Piper | Lab Tests</title>
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
    		<h6 style="font-family:Freestyle;font-size:33px;margin-top:-1px;letter-spacing:1px;color:#f2bd43;background-color:#000;">Lab Test List</h6>
    		<div class="sidebarheadings">Master Lab Tests :</div>
    			<div class="panel-group masterplanactivities" id="accordion1" role="tablist" aria-multiselectable="true" style="max-height:250px;overflow:scroll;overflow-x:hidden;">
			    		<?php
			    		$get_plan_labtests 		= "select t1.PlanCode, t1.LabTestID, t1.RowNo, t1.TestName, t1.DoctorsName,
			    		t1.LabTestRequirements from LAB_TEST_DETAILS1 as t1,PLAN_HEADER as t2 
			    		where t1.PlanCode=t2.PlanCode and t2.MerchantID='$logged_merchantid' and t2.PlanStatus='A'";
		    		$get_plan_labtests_run 	= mysql_query($get_plan_labtests);
		    		$get_plan_labtests_count 	= mysql_num_rows($get_plan_labtests_run);
		    		if($get_plan_labtests_count > 0){
			    		while ($get_labtest_row 	= mysql_fetch_array($get_plan_labtests_run)) {
			    			$labtest_id 	= $get_labtest_row['LabTestID'];
			    			$labtest_row 	= $get_labtest_row['RowNo'];
			    			$labtest_name 	= $get_labtest_row['TestName'];
			    			$fortitle		= $get_labtest_row['TestName'];
			    			$length 		= strlen($labtest_name);
			    			if($length > 12){
			    				$labtest_name = substr($labtest_name,0,12);
			    				$labtest_name = $labtest_name."...";
			    			}
			    			$labtest_doc  	= $get_labtest_row['DoctorsName'];
			    			$labtest_code 	= $get_labtest_row['PlanCode'];
			    			?>
			    			 <div class="panel panel-default plancreationpanel">
					              <div class="panel-heading plancreationaccordion" role="tab" id="heading<?php echo $labtest_code.$labtest_id.$labtest_row; ?>">
					                <h4 class="panel-title" style="text-align:center;">
					                  <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $labtest_code.$labtest_id.$labtest_row; ?>" aria-expanded="false" aria-controls="collapse"><img src="images/rightarrow.png" style="height:12px;width:auto;" align="left" id="arrow<?php echo $labtest_code.$labtest_id.$labtest_row; ?>" onclick='changeimage("<?php echo $labtest_code.$labtest_id.$labtest_row; ?>");'></a>
					                   <span title='<?php echo $fortitle;?>'><?php echo $labtest_name;?></span>
					                  <img src="images/addtoright.png" class="addmasterplanlabtests" align="right" style="height:20px;width:auto;cursor:pointer;background-color:#f2bd43;" id="<?php echo $labtest_code."~~".$labtest_id."~~".$labtest_row; ?>" title="Add to current plan">
					                </h4>
					              </div>
					              <div id="collapse<?php echo $labtest_code.$labtest_id.$labtest_row; ?>" class="panel-collapse collapse plancreationpanelbody" role="tabpanel" aria-labelledby="heading<?php echo $labtest_code.$labtest_id.$labtest_row; ?>">
					                <div class="panel-body"  style="text-align:center;">
					                	<?php
					                		echo $labtest_doc;
					                	?>
					                </div>
					              </div>
					            </div>
			    			<?php
			    		}
		    		} else {
		    			echo "<div style='color:#fff;'>No Lab Tests to show</div>";
		    		}
			    		?>
		    		</div>
		    		<div class="sidebarheadings" style="margin-top: -13px;">Assigned Lab Tests :</div>
		    		<div class="panel-group assignedplanactivities" id="accordion2" role="tablist" aria-multiselectable="true" style="overflow:scroll;overflow-x:hidden;">
			    		<?php
			    		$get_plan_labtests 		= "select  t1.UserID, t1.PlanCode, t1.LabTestID, t1.RowNo, t1.TestName, t1.DoctorsName,t1.LabTestRequirements from USER_LAB_TEST_DETAILS1 as t1,USER_PLAN_HEADER as t2 where t1.PlanCode=t2.PlanCode and t1.UserID=t2.UserID and t2.MerchantID='$logged_merchantid'";
		    		$get_plan_labtests_run 	= mysql_query($get_plan_labtests);
		    		$get_plan_labtests_count 	= mysql_num_rows($get_plan_labtests_run);
		    		if($get_plan_labtests_count > 0){
			    		while ($get_labtest_row 	= mysql_fetch_array($get_plan_labtests_run)) {
			    			$labtest_user   = $get_labtest_row['UserID'];
			    			$labtest_id 	= $get_labtest_row['LabTestID'];
			    			$labtest_row 	= $get_labtest_row['RowNo'];
			    			$labtest_name 	= $get_labtest_row['TestName'];
			    			$fortitle		= $get_labtest_row['TestName'];
			    			$length 		= strlen($labtest_name);
			    			if($length > 12){
			    				$labtest_name = substr($labtest_name,0,12);
			    				$labtest_name = $labtest_name."...";
			    			}
			    			$labtest_doc  	= $get_labtest_row['DoctorsName'];
			    			$labtest_code 	= $get_labtest_row['PlanCode'];
			    			?>
			    			 <div class="panel panel-default plancreationpanel">
					              <div class="panel-heading plancreationaccordion" role="tab" id="heading<?php echo $labtest_code.$labtest_id.$labtest_user.$labtest_row; ?>">
					                <h4 class="panel-title" style="text-align:center;">
					                  <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $labtest_code.$labtest_id.$labtest_user.$labtest_row; ?>" aria-expanded="false" aria-controls="collapse"><img src="images/rightarrow.png" style="height:12px;width:auto;" align="left" id="arrow<?php echo $labtest_code.$labtest_id.$labtest_user.$labtest_row; ?>" onclick='changeimage("<?php echo $labtest_code.$labtest_id.$labtest_user.$labtest_row; ?>");'></a>
					                   <span title='<?php echo $fortitle;?>'><?php echo $labtest_name;?></span>
					                  <img src="images/addtoright.png" class="addassignedplanlabtests" align="right" style="height:20px;width:auto;cursor:pointer;background-color:#f2bd43;" id="<?php echo $labtest_code."~~".$labtest_id."~~".$labtest_user."~~".$labtest_row; ?>" title="Add to current plan">
					                </h4>
					              </div>
					              <div id="collapse<?php echo $labtest_code.$labtest_id.$labtest_user.$labtest_row; ?>" class="panel-collapse collapse plancreationpanelbody" role="tabpanel" aria-labelledby="heading<?php echo $labtest_code.$labtest_id.$labtest_row.$labtest_user; ?>">
					                <div class="panel-body"  style="text-align:center;">
					                	<?php
					                		echo $labtest_doc;
					                	?>
					                </div>
					              </div>
					            </div>
			    			<?php
			    		}
		    		} else {
		    			echo "<div style='color:#fff;'>No Lab Tests to show</div>";
		    		}
			    		?>
		    		</div>
		    </div>
		    </div>
    	</div>
	    	<div class="col-lg-10 col-md-9 col-sm-9 col-sm-12 maincontent">
	    	<div id="dynamicPagePlusActionBar">
	    		<label>
	    			You must first add lab test details. <span id='getlabtests'>Click here</span> to start adding or Select A Template to get started.
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
			$('#8').addClass('active');
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

			var labtestcount = 0;
			$('#thisplantitle').click(function(){
				$('#plandetailsmodal').modal('show');
			});
			$('#editthisplantitle').click(function(){
				$('#plandetailsmodal').modal('show');
			});
			$('#plapiper_pagename').html("Lab Tests");
			 
			$('#addItemButton, #getlabtests').click(function(){
				$.ajax({
					type        : "GET",
					url			: "labTestDefaultPage4.php",
					dataType	: "html",
					success	: function (response)
					{ 
						$('#dynamicPagePlusActionBar').html(response);
						labtestcount = 3;
					 },
					 error: function(error)
					 {
					 	bootbox.alert(error);
					 }
				}); 		
			});
			setTimeout(function() {
		        $("#getlabtests").trigger('click');
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
					//alert(1);
					//$(id).removeClass('in');
					//$('.panel-collapse').removeClass('in');		
				} else {
					//alert(2);
					$(id).addClass('in');
				    $('.panel-collapse').removeClass('in');		
				}

			});
		$('#addLabTest').click(function(){
        labtestcount = labtestcount + 1;
        propercount = propercount + 1;
        $('.deleterow').show();
        var first = "<tr><td><input type='text' name='labtestName"+propercount+"' placeholder='Enter labtest Name..' maxlength='25' id='labtestName"+propercount+"' class='forminputs2'></td><td><input type='text' name='doctorName"+propercount+"' placeholder='Enter Doctor Name..' size='25px' maxlength='25' id='doctorName"+propercount+"' class='forminputs2'></td><td><textarea name='requirements"+propercount+"' id='requirements"+propercount+"' placeholder='Enter Requirements for the labtest..' class='forminputs2'></textarea></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"'></td></tr>";
        var slno  = "<tr><td>"+labtestcount+"</td></tr>";
        $('#aslno > tbody').append(slno);
        $('#adata > tbody').append(first);
        var current_usedlabtestcount = $('#usedlabtestcount').val();
        var new_usedlabtestcount = current_usedlabtestcount+propercount+",";
        $('#usedlabtestcount').val(new_usedlabtestcount);
        $('#labtestcount').val(labtestcount);
      });
			var labtestcount    = $('#labtestcount').val();
			var propercount 	= $('#propercount').val();
						//ON CLICK OF SAVE BUTTON
			$(document).on('click', '#saveAndEdit', function () {
				//alert(1);
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

				var numberOfLabTests = 0;
				var current_usedlabtestcount = $('#usedlabtestcount').val();//ID OF ALL THE FORM ROW ELEMENTS PRESENT SEPERATED BY COMMAS. EG: 1,2,3,
				var result = current_usedlabtestcount.split(',');
				labtestcount = $('#labtestcount').val(); //TOTAL NUMBER OF LAB TEST FIELDS PRESENT CURRENTLY ON THE PAGE
				//bootbox.alert(current_usedlabtestcount);
				for (i = 0; i < labtestcount; ++i) {
					var labtest_name = $('#labtestName'+result[i]).val();
					
					if(!labtest_name == ""){
						if(labtest_name.replace(/\s+/g, '') == ""){
					 		bootbox.alert("Lab Test name cannot be left blank");
					 		$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#labtestName'+result[i]).focus();
							});
					 		$('#labtestName'+result[i]).val("");
							return false;
				 		}
						numberOfLabTests = numberOfLabTests + 1;
						var current_doctor_name = $('#doctorName'+result[i]).val();
						if(current_doctor_name.replace(/\s+/g, '') == ""){
							bootbox.alert("Please enter the doctors name");
							$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#doctorName'+result[i]).focus();
							});
							$('#doctorName'+result[i]).val("");
							return false;
						}
					}
				}
				if(numberOfLabTests == 0){
					bootbox.alert("Please enter atleast one lab tests to continue.");
					$('.bootbox').on('hidden.bs.modal', function() { 
						$('#labtestName1').focus();	    
					});
					return false;
				}
				$('#frm_plan_labtest').submit();
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
        	$('.addmasterplanlabtests').click(function(){
			   var lab_id = $(this).attr('id');  
			   $("input").each(function() {
			    var id = $(this).attr("id");//id of the current textarea
			    var deleted_row_id  = id.replace("labtestName", "");
			    if (id.indexOf("labtestName") >= 0){

			    var labtest_name = $(this).val();
			   	if(labtest_name.replace(/\s+/g, '') == ""){
			      $('#aslno tr:last').remove();
			      //this.parentNode.parentNode.remove();
			      $(this).closest('tr').remove();
			     //alert(labtestcount);
			     var labtestcount = $('#labtestcount').val();
			      labtestcount = parseInt(labtestcount) - 1;
			      //alert(labtestcount);
			      if(labtestcount == 1){
			        $('.deleterow').hide();
			      }
			        var deleted_usedlabtest 		= deleted_row_id+",";
			        var current_usedlabtestcount 	= $('#usedlabtestcount').val();
			        var new_usedlabtestcount  		= current_usedlabtestcount.replace(deleted_usedlabtest, "");
			        $('#usedlabtestcount').val(new_usedlabtestcount);
			        $('#labtestcount').val(labtestcount);
			    }
			    }
			});
			var master 		= lab_id.split("~~");
			var plancode 	= master[0];
			var labtestid 	= master[1];
			var rowno 		= master[2];
			var dataString 	= "plancode="+plancode+"&type=get_master_labtests&testid="+labtestid+"&rowno="+rowno;
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
                    	var labtestcount = $('#labtestcount').val();
					 	var propercount = $('#propercount').val();
					        labtestcount = parseInt(labtestcount) + 1;
					        propercount = parseInt(propercount) + 1;
					        $('.deleterow').show();
					        var first = "<tr><td><input type='text' name='labtestName"+propercount+"' placeholder='Enter labtest Name..' maxlength='25' id='labtestName"+propercount+"' class='forminputs2' value='"+item.TestName+"'></td><td><input type='text' name='doctorName"+propercount+"' placeholder='Enter Doctor Name..' size='25px' maxlength='25' id='doctorName"+propercount+"' class='forminputs2' value='"+item.DoctorsName+"'></td><td><textarea name='requirements"+propercount+"' id='requirements"+propercount+"' placeholder='Enter Requirements for the labtest..' class='forminputs2'>"+item.LabTestRequirements+"</textarea></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"'></td></tr>";
					        var slno  = "<tr><td>"+labtestcount+"</td></tr>";
					        $('#aslno > tbody').append(slno);
					        $('#adata > tbody').append(first);
					        var current_usedlabtestcount = $('#usedlabtestcount').val();
					        var new_usedlabtestcount = current_usedlabtestcount+propercount+",";
					        $('#usedlabtestcount').val(new_usedlabtestcount);
					        $('#labtestcount').val(labtestcount);
					        $('#propercount').val(propercount);
					        $('#labtestName'+propercount).focus();
				    });
                  },
                  error: function(){

                  }
                });
		});
			$('.addassignedplanlabtests').click(function(){
				var lab_id = $(this).attr('id');  
			   $("input").each(function() {
			    var id = $(this).attr("id");//id of the current textarea
			    var deleted_row_id  = id.replace("labtestName", "");
			    if (id.indexOf("labtestName") >= 0){

			    var labtest_name = $(this).val();
			   	if(labtest_name.replace(/\s+/g, '') == ""){
			      $('#aslno tr:last').remove();
			      //this.parentNode.parentNode.remove();
			      $(this).closest('tr').remove();
			     //alert(labtestcount);
			     var labtestcount = $('#labtestcount').val();
			      labtestcount = parseInt(labtestcount) - 1;
			      //alert(labtestcount);
			      if(labtestcount == 1){
			        $('.deleterow').hide();
			      }
			        var deleted_usedlabtest 		= deleted_row_id+",";
			        var current_usedlabtestcount 	= $('#usedlabtestcount').val();
			        var new_usedlabtestcount  		= current_usedlabtestcount.replace(deleted_usedlabtest, "");
			        $('#usedlabtestcount').val(new_usedlabtestcount);
			        $('#labtestcount').val(labtestcount);
			    }
			    }
			});
			var master 		= lab_id.split("~~");
			var plancode 	= master[0];
			var labtestid 	= master[1];
			var rowno 		= master[3];
			var userid 		= master[2]
			var dataString 	= "plancode="+plancode+"&type=get_assigned_labtests&testid="+labtestid+"&rowno="+rowno+"&userid="+userid;
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
                    	var labtestcount = $('#labtestcount').val();
					 	var propercount = $('#propercount').val();
					        labtestcount = parseInt(labtestcount) + 1;
					        propercount = parseInt(propercount) + 1;
					        $('.deleterow').show();
					        var first = "<tr><td><input type='text' name='labtestName"+propercount+"' placeholder='Enter labtest Name..' maxlength='25' id='labtestName"+propercount+"' class='forminputs2' value='"+item.TestName+"'></td><td><input type='text' name='doctorName"+propercount+"' placeholder='Enter Doctor Name..' size='25px' maxlength='25' id='doctorName"+propercount+"' class='forminputs2' value='"+item.DoctorsName+"'></td><td><textarea name='requirements"+propercount+"' id='requirements"+propercount+"' placeholder='Enter Requirements for the labtest..' class='forminputs2'>"+item.LabTestRequirements+"</textarea></td><td><img src='images/closeRow.png' width='25px' height='auto' class='deleterow' id='"+propercount+"'></td></tr>";
					        var slno  = "<tr><td>"+labtestcount+"</td></tr>";
					        $('#aslno > tbody').append(slno);
					        $('#adata > tbody').append(first);
					        var current_usedlabtestcount = $('#usedlabtestcount').val();
					        var new_usedlabtestcount = current_usedlabtestcount+propercount+",";
					        $('#usedlabtestcount').val(new_usedlabtestcount);
					        $('#labtestcount').val(labtestcount);
					        $('#propercount').val(propercount);
					        $('#labtestName'+propercount).focus();
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