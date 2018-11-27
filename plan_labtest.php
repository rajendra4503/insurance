<?php 
session_start();
//ini_set("display_errors","0");
include('include/configinc.php');
include('include/session.php'); 
$current_created_plancode = $_SESSION['current_created_plancode'];
$current_created_planname = $_SESSION['current_created_planname'];
if((isset($_REQUEST['hidden_value']))&&(!empty($_REQUEST['hidden_value']))){
	//echo "<pre>";print_r($_REQUEST);exit;
	$labtestcount    			= mysql_real_escape_string(trim($_REQUEST['labtestcount'])); //Total number of lab tests present on screen
	$usedlabtestcount 			= mysql_real_escape_string(trim($_REQUEST['usedlabtestcount'])); //row ids of medicines present
	$labttestids 				= explode(",", $usedlabtestcount);
	$get_last_labtest_num 		= mysql_query("select max(LabTestID) from LAB_TEST_HEADER1 where PlanCode = '$current_created_plancode'");
	$labtest_count 			= mysql_num_rows($get_last_labtest_num);
	if($labtest_count > 0){
		while($last_labtest_num 	= mysql_fetch_array($get_last_labtest_num)){
			$last_labtest 		= (empty($get_last_labtest_num['max(LabTestID)'])) 		? 0 : $get_last_labtest_num['max(LabTestID)'];
		} 		
	} else {
			$last_labtest      = 0;
	}
	//echo $last_labtest;exit;
	//print_r($medicineids);exit;
	$current_labtest_num 		= $last_labtest + 1;
	$insert_header_details 	= " insert into LAB_TEST_HEADER1 (Plancode, LabTestID, CreatedDate, CreatedBy) values ('$current_created_plancode', '$current_labtest_num', now(), '$logged_userid')";
	//echo $insert_header_details;exit;
	$insert_header_run  	= mysql_query($insert_header_details);
    $count = 1;
	foreach ($labttestids as $ids) {
		if($ids != ""){
			$labtestName = mysql_real_escape_string(trim($_REQUEST["labtestName$ids"]));
			if($labtestName != ""){
			$doctor_name 				= mysql_real_escape_string(trim($_REQUEST["doctorName$ids"]));
			$requirements 				= mysql_real_escape_string(trim($_REQUEST["requirements$ids"]));
			$insert_labttest_details = "insert into LAB_TEST_DETAILS1 (`PlanCode`,`LabTestID`,`RowNo`,`TestName`,`DoctorsName`,`LabTestRequirements`,`CreatedDate`,`CreatedBy`) values ('$current_created_plancode', '$current_labtest_num', '$count', '$labtestName','$doctor_name','$requirements', now(), '$logged_userid');";
			//echo $insert_labttest_details;exit;
			$insert_header_run  	= mysql_query($insert_labttest_details);
			$check_affected_rows= mysql_affected_rows();
		        if($check_affected_rows)
		       { 
		       	//header("Location:plan_labtest.php");
		       } else {
		       	//header("Location:plan_labtest.php");
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
		<title>Plan Piper | Lab Tests</title>
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
			<div id="plantitle"><?php echo $current_created_planname;?>
			<button type="button" class="btns" align="right" id="finished_adding"><img src="images/finishAdd.png" style="height:20px;width:auto;margin-bottom:3px;">&nbsp;FINISH ADDING</button>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<ul class="nav nav-pills nav-justified">
					<?php
					echo $modules;
					?>
					<!--<li role="presentation" class="navbartoptabs"><a href="plan_medication.php">MEDICATION</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_appointments.php">APPOINTMENT</a></li>
					<li role="presentation" class="navbartoptabs"><a href="plan_selftest.php">SELF TEST</a></li>
					<li role="presentation" class="active navbartoptabs"><a href="plan_labtest.php">LAB TEST</a></li>
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
		    		<h6 style="font-family:Freestyle;font-size:180%;margin-top:-7px;letter-spacing:1px;">Lab Tests</h6>
		    	</div>
		    	<div id="listItems">
		    		<button type="button" id="addItemButton" class="btns">Add Tests<img src="images/addItem.png"></button>
		    		<?php 
		    		$get_plan_labtests 		= "select PlanCode, LabTestID, CreatedDate from LAB_TEST_HEADER1 where PlanCode = '$current_created_plancode'";
		    		$get_plan_labtests_run 	= mysql_query($get_plan_labtests);
		    		$get_plan_labtests_count 	= mysql_num_rows($get_plan_labtests_run);
		    		if($get_plan_labtests_count > 0){
			    		while ($get_labtest_row = mysql_fetch_array($get_plan_labtests_run)) {
			    			$labtest_no   = $get_labtest_row['LabTestID'];
			    			$labtest_date = date('d-M-Y',strtotime($get_labtest_row['CreatedDate'])); 
			    			?>
			    			<button type="button" class="btns editlabtestbuttons" id="<?php echo $labtest_no; ?>">
				    			<span>Lab Test - <?php echo $labtest_no;?></span><br>
				    			<span>Created on <?php echo $labtest_date;?></span>
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
	    			You must first add lab test details. <span id='getlabtests'>Click here</span> to start adding or Select A Template to get started.
	    		</label>
	    	</div>
    	</div>
    </div>
		</div>
		<script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/ndatepicker-ui.js"></script>
		<script type="text/javascript" src="js/bootstrap-timepicker.js"></script>
		<script type="text/javascript" src="js/bootbox.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			$('#3').addClass('active');
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

			var labtestcount = 0;
			$('#plapiper_pagename').html("Lab Tests");
			$('#addItemButton, #getlabtests').click(function(){
				$.ajax({
					type        : "GET",
					url			: "labTestDefaultPage.php",
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
		        $("#addItemButton").trigger('click');
		    },1);
						//ON CLICK OF SAVE BUTTON
			$(document).on('click', '#saveAndEdit', function () {
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
					 		$('#labtestName'+result[i]).val("");
					 		$('.bootbox').on('hidden.bs.modal', function() { 
							    $('#labtestName'+result[i]).focus();
							});
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
			$('#addItemButton, #getlabtests').click(function(){
				if(labtestcount > 0){
					var discard = confirm("The current Lab Tests will be discarded. Click OK to continue.");
					if(discard == true){
						labtestcount = 0;
					} else {

					}
				} else {
					labtestcount = 0;
				}
				//bootbox.alert(labtestcount);
				if(labtestcount == 0){
					$.ajax({
						type        : "GET",
						url			: "labTestDefaultPage.php",
						dataType	: "html",
						success	: function (response)
						{ 
							$('#dynamicPagePlusActionBar').html(response);
							labtestcount = 3;
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
			//EDIT LABTEST BUTTON CLICKED - FROM SIDE PANEL
			$('.editlabtestbuttons').click(function(){
			var appoid = $(this).attr('id');
				//bootbox.alert(appoid);
			window.location.href = "edit_plan_labtest.php?id="+appoid;
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
</html>